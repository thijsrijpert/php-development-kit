<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 *
 * This code is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License version 2 only, as
 * published by the Free Software Foundation.  This particular file is
 * designated as subject to the "Classpath" exception as provided in the
 * LICENSE file that accompanied this code.
 *
 * This code is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
 * version 2 for more details (a copy is included in the LICENSE file that
 * accompanied this code).
 *
 * You should have received a copy of the GNU General Public License version
 * 2 along with this work; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */
/*
 * Copyright (c) 2012, 2021, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */
namespace jhp\util\stream;

use jhp\nio\file\Files;
use jhp\util\collection\Comparator;
use jhp\util\collection\ICollection;
use jhp\util\collection\IList;
use jhp\util\function\BiConsumer;
use jhp\util\function\BiFunction;
use jhp\util\function\BinaryOperator;
use jhp\util\function\Consumer;
use jhp\util\function\GFunction;
use jhp\util\function\IntFunction;
use jhp\util\function\Predicate;
use jhp\util\function\ToFloatFunction;
use jhp\util\function\ToIntFunction;
use jhp\util\Optional;

/**
 * A sequence of elements supporting sequential and parallel aggregate
 * operations.  The following example illustrates an aggregate operation using
 * {@link Stream} and {@link IntStream}:
 *
 * <pre>{@code
 *     int sum = widgets.stream()
 *                      .filter(w -> w.getColor() == RED)
 *                      .mapToInt(w -> w.getWeight())
 *                      .sum();
 * }</pre>
 *
 * In this example, widgets is a ICollection<Widget>.  We create
 * a stream of Widget objects via {@link ICollection#stream ICollection.stream()},
 * filter it to produce a stream containing only the red widgets, and then
 * transform it into a stream of int values representing the weight of
 * each red widget. Then this stream is summed to produce a total weight.
 *
 * <p>In addition to Stream, which is a stream of object references,
 * there are primitive specializations for {@link IntStream}, {@link LongStream},
 * and {@link DoubleStream}, all of which are referred to as "streams" and
 * conform to the characteristics and restrictions described here.
 *
 * <p>To perform a computation, stream
 * <a href="package-summary.html#StreamOps">operations</a> are composed into a
 * <em>stream pipeline</em>.  A stream pipeline consists of a source (which
 * might be an array, a collection, a generator function, an I/O channel,
 * etc), zero or more <em>intermediate operations</em> (which transform a
 * stream into another stream, such as {@link Stream#filter(Predicate)}), and a
 * <em>terminal operation</em> (which produces a result or side-effect, such
 * as {@link Stream#count()} or {@link Stream#forEach(Consumer)}).
 * Streams are lazy; computation on the source data is only performed when the
 * terminal operation is initiated, and source elements are consumed only
 * as needed.
 *
 * <p>A stream implementation is permitted significant latitude in optimizing
 * the computation of the result.  For example, a stream implementation is free
 * to elide operations (or entire stages) from a stream pipeline -- and
 * therefore elide invocation of behavioral parameters -- if it can prove that
 * it would not affect the result of the computation.  This means that
 * side-effects of behavioral parameters may not always be executed and should
 * not be relied upon, unless otherwise specified (such as by the terminal
 * operations forEach and forEachOrdered). (For a specific
 * example of such an optimization, see the API note documented on the
 * {@link #count} operation.  For more detail, see the
 * <a href="package-summary.html#SideEffects">side-effects</a> section of the
 * stream package documentation.)
 *
 * <p>Collections and streams, while bearing some superficial similarities,
 * have different goals.  Collections are primarily concerned with the efficient
 * management of, and access to, their elements.  By contrast, streams do not
 * provide a means to directly access or manipulate their elements, and are
 * instead concerned with declaratively describing their source and the
 * computational operations which will be performed in aggregate on that source.
 * However, if the provided stream operations do not offer the desired
 * functionality, the {@link #iterator()} and {@link #spliterator()} operations
 * can be used to perform a controlled traversal.
 *
 * <p>A stream pipeline, like the "widgets" example above, can be viewed as
 * a <em>query</em> on the stream source.  Unless the source was explicitly
 * designed for concurrent modification (such as a {@link ConcurrentHashMap}),
 * unpredictable or erroneous behavior may result from modifying the stream
 * source while it is being queried.
 *
 * <p>Most stream operations accept parameters that describe user-specified
 * behavior, such as the lambda expression w -> w.getWeight() passed to
 * mapToInt in the example above.  To preserve correct behavior,
 * these <em>behavioral parameters</em>:
 * <ul>
 * <li>must be <a href="package-summary.html#NonInterference">non-interfering</a>
 * (they do not modify the stream source); and</li>
 * <li>in most cases must be <a href="package-summary.html#Statelessness">stateless</a>
 * (their result should not depend on any state that might change during execution
 * of the stream pipeline).</li>
 * </ul>
 *
 * <p>Such parameters are always instances of a
 * <a href="../function/package-summary.html">functional interface</a> such
 * as {@link java.internal.function.Function}, and are often lambda expressions or
 * method references.  Unless otherwise specified these parameters must be
 * <em>non-null</em>.
 *
 * <p>A stream should be operated on (invoking an intermediate or terminal stream
 * operation) only once.  This rules out, for example, "forked" streams, where
 * the same source feeds two or more pipelines, or multiple traversals of the
 * same stream.  A stream implementation may throw {@link IllegalStateException}
 * if it detects that the stream is being reused. However, since some stream
 * operations may return their receiver rather than a new stream object, it may
 * not be possible to detect reuse in all cases.
 *
 * <p>Streams have a {@link #close()} method and implement {@link AutoCloseable}.
 * Operating on a stream after it has been closed will throw {@link IllegalStateException}.
 * Most stream instances do not actually need to be closed after use, as they
 * are backed by collections, arrays, or generating functions, which require no
 * special resource management. Generally, only streams whose source is an IO channel,
 * such as those returned by {@link Files#lines(Path)}, will require closing. If a
 * stream does require closing, it must be opened as a resource within a try-with-resources
 * statement or similar control structure to ensure that it is closed promptly after its
 * operations have completed.
 *
 * <p>Stream pipelines may execute either sequentially or in
 * <a href="package-summary.html#Parallelism">parallel</a>.  This
 * execution mode is a property of the stream.  Streams are created
 * with an initial choice of sequential or parallel execution.  (For example,
 * {@link ICollection#stream() ICollection.stream()} creates a sequential stream,
 * and {@link ICollection#parallelStream() ICollection.parallelStream()} creates
 * a parallel one.)  This choice of execution mode may be modified by the
 * {@link #sequential()} or {@link #parallel()} methods, and may be queried with
 * the {@link #isParallel()} method.
 *
 * @since 1.8
 * @see IntStream
 * @see LongStream
 * @see DoubleStream
 * @see <a href="package-summary.html">java.internal.stream</a>
 */
interface Stream extends BaseStream
{

    /**
     * Returns a stream consisting of the elements of this stream that match
     * the given predicate.
     *
     * <p>This is an <a href="package-summary.html#StreamOps">intermediate
     * operation</a>.
     *
     * @param Predicate $predicate a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                  <a href="package-summary.html#Statelessness">stateless</a>
     *                  predicate to apply to each element to determine if it
     *                  should be included
     * @return Stream the new stream
     */
    function filter(Predicate $predicate): Stream;

    /**
     * Returns a stream consisting of the results of applying the given
     * function to the elements of this stream.
     *
     * <p>This is an <a href="package-summary.html#StreamOps">intermediate
     * operation</a>.
     *
     * @param GFunction $mapper a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *               <a href="package-summary.html#Statelessness">stateless</a>
     *               function to apply to each element
     * @return Stream the new stream
     */
    function map(GFunction $mapper): Stream;

    /**
     * Returns an IntStream consisting of the results of applying the
     * given function to the elements of this stream.
     *
     * <p>This is an <a href="package-summary.html#StreamOps">
     *     intermediate operation</a>.
     *
     * @param ToIntFunction $mapper a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *               <a href="package-summary.html#Statelessness">stateless</a>
     *               function to apply to each element
     * @return IntStream the new stream
     */
    function mapToInt(ToIntFunction $mapper): IntStream;

    /**
     * Returns a DoubleStream consisting of the results of applying the
     * given function to the elements of this stream.
     *
     * <p>This is an <a href="package-summary.html#StreamOps">intermediate
     * operation</a>.
     *
     * @param ToFloatFunction $mapper a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *               <a href="package-summary.html#Statelessness">stateless</a>
     *               function to apply to each element
     * @return FloatStream the new stream
     */
    function mapToFloat(ToFloatFunction $mapper): FloatStream;

    /**
     * Returns a stream consisting of the results of replacing each element of
     * this stream with the contents of a mapped stream produced by applying
     * the provided mapping function to each element.  Each mapped stream is
     * {@link java.util.stream.BaseStream#close() closed} after its contents
     * have been placed into this stream.  (If a mapped stream is null
     * an empty stream is used, instead.)
     *
     * <p>This is an <a href="package-summary.html#StreamOps">intermediate
     * operation</a>.
     *
     * @apiNote
     * The flatMap() operation has the effect of applying a one-to-many
     * transformation to the elements of the stream, and then flattening the
     * resulting elements into a new stream.
     *
     * <p><b>Examples.</b>
     *
     * <p>If orders is a stream of purchase orders, and each purchase
     * order contains a collection of line items, then the following produces a
     * stream containing all the line items in all the orders:
     * <pre>{@code
     *     orders.flatMap(order -> order.getLineItems().stream())...
     * }</pre>
     *
     * <p>If path is the path to a file, then the following produces a
     * stream of the words contained in that file:
     * <pre>{@code
     *     Stream<String> lines = Files.lines(path, StandardCharsets.UTF_8);
     *     Stream<String> words = lines.flatMap(line -> Stream.of(line.split(" +")));
     * }</pre>
     * The mapper function passed to flatMap splits a line,
     * using a simple regular expression, into an array of words, and then
     * creates a stream of words from that array.
     *
     * @param GFunction $mapper a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *               <a href="package-summary.html#Statelessness">stateless</a>
     *               function to apply to each element which produces a stream
     *               of new values
     * @return Stream the new stream
     * @see #mapMulti
     */
    function flatMap(GFunction $mapper): Stream;

    /**
     * Returns an IntStream consisting of the results of replacing each
     * element of this stream with the contents of a mapped stream produced by
     * applying the provided mapping function to each element.  Each mapped
     * stream is {@link java.util.stream.BaseStream#close() closed} after its
     * contents have been placed into this stream.  (If a mapped stream is
     * null an empty stream is used, instead.)
     *
     * <p>This is an <a href="package-summary.html#StreamOps">intermediate
     * operation</a>.
     *
     * @param GFunction $mapper a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *               <a href="package-summary.html#Statelessness">stateless</a>
     *               function to apply to each element which produces a stream
     *               of new values
     * @return IntStream the new stream
     * @see #flatMap(Function)
     */
    function flatMapToInt(GFunction $mapper): IntStream;

    /**
     * Returns an DoubleStream consisting of the results of replacing
     * each element of this stream with the contents of a mapped stream produced
     * by applying the provided mapping function to each element.  Each mapped
     * stream is {@link java.util.stream.BaseStream#close() closed} after its
     * contents have placed been into this stream.  (If a mapped stream is
     * null an empty stream is used, instead.)
     *
     * <p>This is an <a href="package-summary.html#StreamOps">intermediate
     * operation</a>.
     *
     * @param GFunction $mapper a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *               <a href="package-summary.html#Statelessness">stateless</a>
     *               function to apply to each element which produces a stream
     *               of new values
     * @return FloatStream the new stream
     * @see #flatMap(Function)
     */
    function flatMapToFloat(GFunction $mapper): FloatStream;

    // THE EXAMPLES USED IN THE JAVADOC MUST BE IN SYNC WITH THEIR CORRESPONDING
    // TEST IN test/jdk/java/internal/stream/examples/JavadocExamples.java.
    /**
     * Returns a stream consisting of the results of replacing each element of
     * this stream with multiple elements, specifically zero or more elements.
     * Replacement is performed by applying the provided mapping function to each
     * element in conjunction with a {@linkplain Consumer consumer} argument
     * that accepts replacement elements. The mapping function calls the consumer
     * zero or more times to provide the replacement elements.
     *
     * <p>This is an <a href="package-summary.html#StreamOps">intermediate
     * operation</a>.
     *
     * <p>If the {@linkplain Consumer consumer} argument is used outside the scope of
     * its application to the mapping function, the results are undefined.
     *
     * @implSpec
     * The default implementation invokes {@link #flatMap flatMap} on this stream,
     * passing a function that behaves as follows. First, it calls the mapper function
     * with a Consumer that accumulates replacement elements into a newly created
     * internal buffer. When the mapper function returns, it creates a stream from the
     * internal buffer. Finally, it returns this stream to flatMap.
     *
     * @apiNote
     * This method is similar to {@link #flatMap flatMap} in that it applies a one-to-many
     * transformation to the elements of the stream and flattens the result elements
     * into a new stream. This method is preferable to flatMap in the following
     * circumstances:
     * <ul>
     * <li>When replacing each stream element with a small (possibly zero) number of
     * elements. Using this method avoids the overhead of creating a new Stream instance
     * for every group of result elements, as required by flatMap.</li>
     * <li>When it is easier to use an imperative approach for generating result
     * elements than it is to return them in the form of a Stream.</li>
     * </ul>
     *
     * <p>If a lambda expression is provided as the mapper function argument, additional type
     * information may be necessary for proper inference of the element type <R> of
     * the returned stream. This can be provided in the form of explicit type declarations for
     * the lambda parameters or as an explicit type argument to the mapMulti call.
     *
     * <p><b>Examples</b>
     *
     * <p>Given a stream of Number objects, the following
     * produces a list containing only the Integer objects:
     * <pre>{@code
     *     Stream<Number> numbers = ... ;
     *     List<Integer> integers = numbers.<Integer>mapMulti((number, consumer) -> {
     *             if (number instanceof Integer i)
     *                 consumer.accept(i);
     *         })
     *         .collect(Collectors.toList());
     * }</pre>
     *
     * <p>If we have an Iterable<Object> and need to recursively expand its elements
     * that are themselves of type Iterable, we can use mapMulti as follows:
     * <pre>{@code
     * class C {
     *     static void expandIterable(Object e, Consumer<Object> c) {
     *         if (e instanceof Iterable<?> elements) {
     *             for (Object ie : elements) {
     *                 expandIterable(ie, c);
     *             }
     *         } else if (e != null) {
     *             c.accept(e);
     *         }
     *     }
     *
     *     public static void main(String[] args) {
     *         var nestedList = List.of(1, List.of(2, List.of(3, 4)), 5);
     *         Stream<Object> expandedStream = nestedList.stream().mapMulti(C::expandIterable);
     *     }
     * }
     * }</pre>
     *
     * @param BiConsumer $mapper a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *               <a href="package-summary.html#Statelessness">stateless</a>
     *               function that generates replacement elements
     * @return Stream the new stream
     * @see #flatMap flatMap
     * @since 16
     */
    function mapMulti(BiConsumer $mapper): Stream;

    /**
     * Returns an IntStream consisting of the results of replacing each
     * element of this stream with multiple elements, specifically zero or more
     * elements.
     * Replacement is performed by applying the provided mapping function to each
     * element in conjunction with a {@linkplain IntConsumer consumer} argument
     * that accepts replacement elements. The mapping function calls the consumer
     * zero or more times to provide the replacement elements.
     *
     * <p>This is an <a href="package-summary.html#StreamOps">intermediate
     * operation</a>.
     *
     * <p>If the {@linkplain IntConsumer consumer} argument is used outside the scope of
     * its application to the mapping function, the results are undefined.
     *
     * @implSpec
     * The default implementation invokes {@link #flatMapToInt} on this stream,
     * passing a function that behaves as follows. First, it calls the mapper function
     * with an IntConsumer that accumulates replacement elements into a newly created
     * internal buffer. When the mapper function returns, it creates an IntStream from
     * the internal buffer. Finally, it returns this stream to flatMapToInt.
     *
     * @param BiConsumer $mapper a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *               <a href="package-summary.html#Statelessness">stateless</a>
     *               function that generates replacement elements
     * @return IntStream the new stream
     * @see #mapMulti mapMulti
     * @since 16
     */
    function mapMultiToInt(BiConsumer $mapper): IntStream;

    /**
     * Returns a DoubleStream consisting of the results of replacing each
     * element of this stream with multiple elements, specifically zero or more
     * elements.
     * Replacement is performed by applying the provided mapping function to each
     * element in conjunction with a {@linkplain DoubleConsumer consumer} argument
     * that accepts replacement elements. The mapping function calls the consumer
     * zero or more times to provide the replacement elements.
     *
     * <p>This is an <a href="package-summary.html#StreamOps">intermediate
     * operation</a>.
     *
     * <p>If the {@linkplain DoubleConsumer consumer} argument is used outside the scope of
     * its application to the mapping function, the results are undefined.
     *
     * @implSpec
     * The default implementation invokes {@link #flatMapToDouble flatMapToDouble} on this stream,
     * passing a function that behaves as follows. First, it calls the mapper function
     * with an DoubleConsumer that accumulates replacement elements into a newly created
     * internal buffer. When the mapper function returns, it creates a DoubleStream from
     * the internal buffer. Finally, it returns this stream to flatMapToDouble.
     *
     * @param BiConsumer $mapper a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *               <a href="package-summary.html#Statelessness">stateless</a>
     *               function that generates replacement elements
     * @return FloatStream the new stream
     * @see #mapMulti mapMulti
     * @since 16
     */
    function mapMultiToDouble(BiConsumer $mapper): FloatStream;
    /**
     * Returns a stream consisting of the distinct elements (according to
     * {@link Object#equals(Object)}) of this stream.
     *
     * <p>For ordered streams, the selection of distinct elements is stable
     * (for duplicated elements, the element appearing first in the encounter
     * order is preserved.)  For unordered streams, no stability guarantees
     * are made.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">stateful
     * intermediate operation</a>.
     *
     * @apiNote
     * Preserving stability for distinct() in parallel pipelines is
     * relatively expensive (requires that the operation act as a full barrier,
     * with substantial buffering overhead), and stability is often not needed.
     * Using an unordered stream source (such as {@link #generate(Supplier)})
     * or removing the ordering constraint with {@link #unordered()} may result
     * in significantly more efficient execution for distinct() in parallel
     * pipelines, if the semantics of your situation permit.  If consistency
     * with encounter order is required, and you are experiencing poor performance
     * or memory utilization with distinct() in parallel pipelines,
     * switching to sequential execution with {@link #sequential()} may improve
     * performance.
     *
     * @return Stream the new stream
     */
    function distinct(): Stream;

    /**
     * Returns a stream consisting of the elements of this stream, sorted
     * according to the provided Comparator.
     *
     * <p>For ordered streams, the sort is stable.  For unordered streams, no
     * stability guarantees are made.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">stateful
     * intermediate operation</a>.
     *
     * @param ?Comparator $comparator a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                   <a href="package-summary.html#Statelessness">stateless</a>
     *                   Comparator to be used to compare stream elements
     * @return Stream the new stream
     */
    function sorted(?Comparator $comparator = null): Stream;

    /**
     * Returns a stream consisting of the elements of this stream, additionally
     * performing the provided action on each element as elements are consumed
     * from the resulting stream.
     *
     * <p>This is an <a href="package-summary.html#StreamOps">intermediate
     * operation</a>.
     *
     * <p>For parallel stream pipelines, the action may be called at
     * whatever time and in whatever thread the element is made available by the
     * upstream operation.  If the action modifies shared state,
     * it is responsible for providing the required synchronization.
     *
     * @apiNote This method exists mainly to support debugging, where you want
     * to see the elements as they flow past a certain point in a pipeline:
     * <pre>{@code
     *     Stream.of("one", "two", "three", "four")
     *         .filter(e -> e.length() > 3)
     *         .peek(e -> System.out.println("Filtered value: " + e))
     *         .map(String::toUpperCase)
     *         .peek(e -> System.out.println("Mapped value: " + e))
     *         .collect(Collectors.toList());
     * }</pre>
     *
     * <p>In cases where the stream implementation is able to optimize away the
     * production of some or all the elements (such as with short-circuiting
     * operations like findFirst, or in the example described in
     * {@link #count}), the action will not be invoked for those elements.
     *
     * @param Consumer $action a <a href="package-summary.html#NonInterference">
     *                 non-interfering</a> action to perform on the elements as
     *                 they are consumed from the stream
     * @return Stream the new stream
     */
    function peek(Consumer $action): Stream;

    /**
     * Returns a stream consisting of the elements of this stream, truncated
     * to be no longer than maxSize in length.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">short-circuiting
     * stateful intermediate operation</a>.
     *
     * @apiNote
     * While limit() is generally a cheap operation on sequential
     * stream pipelines, it can be quite expensive on ordered parallel pipelines,
     * especially for large values of maxSize, since limit(n)
     * is constrained to return not just any <em>n</em> elements, but the
     * <em>first n</em> elements in the encounter order.  Using an unordered
     * stream source (such as {@link #generate(Supplier)}) or removing the
     * ordering constraint with {@link #unordered()} may result in significant
     * speedups of limit() in parallel pipelines, if the semantics of
     * your situation permit.  If consistency with encounter order is required,
     * and you are experiencing poor performance or memory utilization with
     * limit() in parallel pipelines, switching to sequential execution
     * with {@link #sequential()} may improve performance.
     *
     * @param int $maxSize the number of elements the stream should be limited to
     * @return Stream the new stream
     */
    function limit(int $maxSize): Stream;

    /**
     * Returns a stream consisting of the remaining elements of this stream
     * after discarding the first n elements of the stream.
     * If this stream contains fewer than n elements then an
     * empty stream will be returned.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">stateful
     * intermediate operation</a>.
     *
     * @apiNote
     * While skip() is generally a cheap operation on sequential
     * stream pipelines, it can be quite expensive on ordered parallel pipelines,
     * especially for large values of n, since skip(n)
     * is constrained to skip not just any <em>n</em> elements, but the
     * <em>first n</em> elements in the encounter order.  Using an unordered
     * stream source (such as {@link #generate(Supplier)}) or removing the
     * ordering constraint with {@link #unordered()} may result in significant
     * speedups of skip() in parallel pipelines, if the semantics of
     * your situation permit.  If consistency with encounter order is required,
     * and you are experiencing poor performance or memory utilization with
     * skip() in parallel pipelines, switching to sequential execution
     * with {@link #sequential()} may improve performance.
     *
     * @param int $n the number of leading elements to skip
     * @return Stream the new stream
     */
    function skip(int $n): Stream;

    /**
     * Returns, if this stream is ordered, a stream consisting of the longest
     * prefix of elements taken from this stream that match the given predicate.
     * Otherwise, returns, if this stream is unordered, a stream consisting of a
     * subset of elements taken from this stream that match the given predicate.
     *
     * <p>If this stream is ordered then the longest prefix is a contiguous
     * sequence of elements of this stream that match the given predicate.  The
     * first element of the sequence is the first element of this stream, and
     * the element immediately following the last element of the sequence does
     * not match the given predicate.
     *
     * <p>If this stream is unordered, and some (but not all) elements of this
     * stream match the given predicate, then the behavior of this operation is
     * nondeterministic; it is free to take any subset of matching elements
     * (which includes the empty set).
     *
     * <p>Independent of whether this stream is ordered or unordered if all
     * elements of this stream match the given predicate then this operation
     * takes all elements (the result is the same as the input), or if no
     * elements of the stream match the given predicate then no elements are
     * taken (the result is an empty stream).
     *
     * <p>This is a <a href="package-summary.html#StreamOps">short-circuiting
     * stateful intermediate operation</a>.
     *
     * @implSpec
     * The default implementation obtains the {@link #spliterator() spliterator}
     * of this stream, wraps that spliterator so as to support the semantics
     * of this operation on traversal, and returns a new stream associated with
     * the wrapped spliterator.  The returned stream preserves the execution
     * characteristics of this stream (namely parallel or sequential execution
     * as per {@link #isParallel()}) but the wrapped spliterator may choose to
     * not support splitting.  When the returned stream is closed, the close
     * handlers for both the returned and this stream are invoked.
     *
     * @apiNote
     * While takeWhile() is generally a cheap operation on sequential
     * stream pipelines, it can be quite expensive on ordered parallel
     * pipelines, since the operation is constrained to return not just any
     * valid prefix, but the longest prefix of elements in the encounter order.
     * Using an unordered stream source (such as {@link #generate(Supplier)}) or
     * removing the ordering constraint with {@link #unordered()} may result in
     * significant speedups of takeWhile() in parallel pipelines, if the
     * semantics of your situation permit.  If consistency with encounter order
     * is required, and you are experiencing poor performance or memory
     * utilization with takeWhile() in parallel pipelines, switching to
     * sequential execution with {@link #sequential()} may improve performance.
     *
     * @param Predicate $predicate a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                  <a href="package-summary.html#Statelessness">stateless</a>
     *                  predicate to apply to elements to determine the longest
     *                  prefix of elements.
     * @return Stream the new stream
     * @since 9
     */
    function takeWhile(Predicate $predicate): Stream;

    /**
     * Returns, if this stream is ordered, a stream consisting of the remaining
     * elements of this stream after dropping the longest prefix of elements
     * that match the given predicate.  Otherwise, returns, if this stream is
     * unordered, a stream consisting of the remaining elements of this stream
     * after dropping a subset of elements that match the given predicate.
     *
     * <p>If this stream is ordered then the longest prefix is a contiguous
     * sequence of elements of this stream that match the given predicate.  The
     * first element of the sequence is the first element of this stream, and
     * the element immediately following the last element of the sequence does
     * not match the given predicate.
     *
     * <p>If this stream is unordered, and some (but not all) elements of this
     * stream match the given predicate, then the behavior of this operation is
     * nondeterministic; it is free to drop any subset of matching elements
     * (which includes the empty set).
     *
     * <p>Independent of whether this stream is ordered or unordered if all
     * elements of this stream match the given predicate then this operation
     * drops all elements (the result is an empty stream), or if no elements of
     * the stream match the given predicate then no elements are dropped (the
     * result is the same as the input).
     *
     * <p>This is a <a href="package-summary.html#StreamOps">stateful
     * intermediate operation</a>.
     *
     * @implSpec
     * The default implementation obtains the {@link #spliterator() spliterator}
     * of this stream, wraps that spliterator so as to support the semantics
     * of this operation on traversal, and returns a new stream associated with
     * the wrapped spliterator.  The returned stream preserves the execution
     * characteristics of this stream (namely parallel or sequential execution
     * as per {@link #isParallel()}) but the wrapped spliterator may choose to
     * not support splitting.  When the returned stream is closed, the close
     * handlers for both the returned and this stream are invoked.
     *
     * @apiNote
     * While dropWhile() is generally a cheap operation on sequential
     * stream pipelines, it can be quite expensive on ordered parallel
     * pipelines, since the operation is constrained to return not just any
     * valid prefix, but the longest prefix of elements in the encounter order.
     * Using an unordered stream source (such as {@link #generate(Supplier)}) or
     * removing the ordering constraint with {@link #unordered()} may result in
     * significant speedups of dropWhile() in parallel pipelines, if the
     * semantics of your situation permit.  If consistency with encounter order
     * is required, and you are experiencing poor performance or memory
     * utilization with dropWhile() in parallel pipelines, switching to
     * sequential execution with {@link #sequential()} may improve performance.
     *
     * @param Predicate $predicate a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                  <a href="package-summary.html#Statelessness">stateless</a>
     *                  predicate to apply to elements to determine the longest
     *                  prefix of elements.
     * @return Stream the new stream
     * @since 9
     */
    function dropWhile(Predicate $predicate): Stream;

    /**
     * Performs an action for each element of this stream.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">terminal
     * operation</a>.
     *
     * <p>The behavior of this operation is explicitly nondeterministic.
     * For parallel stream pipelines, this operation does <em>not</em>
     * guarantee to respect the encounter order of the stream, as doing so
     * would sacrifice the benefit of parallelism.  For any given element, the
     * action may be performed at whatever time and in whatever thread the
     * library chooses.  If the action accesses shared state, it is
     * responsible for providing the required synchronization.
     *
     * @param Consumer $action a <a href="package-summary.html#NonInterference">
     *               non-interfering</a> action to perform on the elements
     */
    function forEach(Consumer $action): void;

    /**
     * Performs an action for each element of this stream, in the encounter
     * order of the stream if the stream has a defined encounter order.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">terminal
     * operation</a>.
     *
     * <p>This operation processes the elements one at a time, in encounter
     * order if one exists.  Performing the action for one element
     * <a href="../concurrent/package-summary.html#MemoryVisibility"><i>happens-before</i></a>
     * performing the action for subsequent elements, but for any given element,
     * the action may be performed in whatever thread the library chooses.
     *
     * @param Consumer $action a <a href="package-summary.html#NonInterference">
     *               non-interfering</a> action to perform on the elements
     * @see #forEach(Consumer)
     */
    function forEachOrdered(Consumer $action): void;

    /**
     * Returns an array containing the elements of this stream, using the
     * provided generator function to allocate the returned array, as
     * well as any additional arrays that might be required for a partitioned
     * execution or for resizing.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">terminal
     * operation</a>.
     *
     * @apiNote
     * The generator function takes an integer, which is the size of the
     * desired array, and produces an array of the desired size.  This can be
     * concisely expressed with an array constructor reference:
     * <pre>{@code
     *     Person[] men = people.stream()
     *                          .filter(p -> p.getGender() == MALE)
     *                          .toArray(Person[]::new);
     * }</pre>
     *
     * @param ?IntFunction $generator a function which produces a new array of the desired
     *                  type and the provided length
     * @return array containing the elements in this stream
     */
    function toArray(?IntFunction $generator = null): array;

    /**
     * Performs a <a href="package-summary.html#Reduction">reduction</a> on the
     * elements of this stream, using the provided identity, accumulation and
     * combining functions.  This is equivalent to:
     * <pre>{@code
     *     U result = identity;
     *     for (T element : this stream)
     *         result = accumulator.apply(result, element)
     *     return result;
     * }</pre>
     *
     * but is not constrained to execute sequentially.
     *
     * <p>The identity value must be an identity for the combiner
     * function.  This means that for all u, combiner(identity, u)
     * is equal to u.  Additionally, the combiner function
     * must be compatible with the accumulator function; for all
     * u and t, the following must hold:
     * <pre>{@code
     *     combiner.apply(u, accumulator.apply(identity, t)) == accumulator.apply(u, t)
     * }</pre>
     *
     * <p>This is a <a href="package-summary.html#StreamOps">terminal
     * operation</a>.
     *
     * @apiNote Many reductions using this form can be represented more simply
     * by an explicit combination of map and reduce operations.
     * The accumulator function acts as a fused mapper and accumulator,
     * which can sometimes be more efficient than separate mapping and reduction,
     * such as when knowing the previously reduced value allows you to avoid
     * some computation.
     *
     * @param ?object $identity the identity value for the combiner function
     * @param BiFunction|BinaryOperator $accumulator an <a href="package-summary.html#Associativity">associative</a>,
     *                    <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                    <a href="package-summary.html#Statelessness">stateless</a>
     *                    function for incorporating an additional element into a result
     * @param ?BinaryOperator $combiner an <a href="package-summary.html#Associativity">associative</a>,
     *                    <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                    <a href="package-summary.html#Statelessness">stateless</a>
     *                    function for combining two values, which must be
     *                    compatible with the accumulator function
     * @return Optional the result of the reduction
     */
    function reduce(?object $identity, BiFunction|BinaryOperator $accumulator, ?BinaryOperator $combiner): Optional;

    /**
     * Performs a <a href="package-summary.html#MutableReduction">mutable
     * reduction</a> operation on the elements of this stream using a
     * Collector.  A Collector
     * encapsulates the functions used as arguments to
     * {@link #collect(Supplier, BiConsumer, BiConsumer)}, allowing for reuse of
     * collection strategies and composition of collect operations such as
     * multiple-level grouping or partitioning.
     *
     * <p>If the stream is parallel, and the Collector
     * is {@link Collector.Characteristics#CONCURRENT concurrent}, and
     * either the stream is unordered or the collector is
     * {@link Collector.Characteristics#UNORDERED unordered},
     * then a concurrent reduction will be performed (see {@link Collector} for
     * details on concurrent reduction.)
     *
     * <p>This is a <a href="package-summary.html#StreamOps">terminal
     * operation</a>.
     *
     * <p>When executed in parallel, multiple intermediate results may be
     * instantiated, populated, and merged so as to maintain isolation of
     * mutable data structures.  Therefore, even when executed in parallel
     * with non-thread-safe data structures (such as ArrayList), no
     * additional synchronization is needed for a parallel reduction.
     *
     * @apiNote
     * The following will accumulate strings into a List:
     * <pre>{@code
     *     List<String> asList = stringStream.collect(Collectors.toList());
     * }</pre>
     *
     * <p>The following will classify Person objects by city:
     * <pre>{@code
     *     Map<String, List<Person>> peopleByCity
     *         = personStream.collect(Collectors.groupingBy(Person::getCity));
     * }</pre>
     *
     * <p>The following will classify Person objects by state and city,
     * cascading two Collectors together:
     * <pre>{@code
     *     Map<String, Map<String, List<Person>>> peopleByStateAndCity
     *         = personStream.collect(Collectors.groupingBy(Person::getState,
     *                                                      Collectors.groupingBy(Person::getCity)));
     * }</pre>
     *
     * @param Collector $collector the Collector describing the reduction
     * @return object the result of the reduction
     * @see #collect(Supplier, BiConsumer, BiConsumer)
     * @see Collectors
     */
    function collect(Collector $collector): object;

    /**
     * Accumulates the elements of this stream into a List. The elements in
     * the list will be in this stream's encounter order, if one exists. The returned List
     * is unmodifiable; calls to any mutator method will always cause
     * UnsupportedOperationException to be thrown. There are no
     * guarantees on the implementation type or serializability of the returned List.
     *
     * <p>The returned instance may be <a href="{@docRoot}/java.base/java/lang/doc-files/ValueBased.html">value-based</a>.
     * Callers should make no assumptions about the identity of the returned instances.
     * Identity-sensitive operations on these instances (reference equality (==),
     * identity hash code, and synchronization) are unreliable and should be avoided.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">terminal operation</a>.
     *
     * @apiNote If more control over the returned object is required, use
     * {@link Collectors#toCollection(Supplier)}.
     *
     * @implSpec The implementation in this interface returns a List produced as if by the following:
     * <pre>{@code
     * Collections.unmodifiableList(new ArrayList<>(Arrays.asList(this.toArray())))
     * }</pre>
     *
     * @implNote Most instances of Stream will override this method and provide an implementation
     * that is highly optimized compared to the implementation in this interface.
     *
     * @return IList a List containing the stream elements
     *
     * @since 16
     */
    function toList(): IList;

    /**
     * Returns the minimum element of this stream according to the provided
     * Comparator.  This is a special case of a
     * <a href="package-summary.html#Reduction">reduction</a>.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">terminal operation</a>.
     *
     * @param Comparator $comparator a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                   <a href="package-summary.html#Statelessness">stateless</a>
     *                   Comparator to compare elements of this stream
     * @return Optional an optional describing the minimum element of this stream,
     * or an empty optional if the stream is empty
     */
    function min(Comparator $comparator): Optional;

    /**
     * Returns the maximum element of this stream according to the provided
     * Comparator.  This is a special case of a
     * <a href="package-summary.html#Reduction">reduction</a>.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">terminal
     * operation</a>.
     *
     * @param Comparator $comparator a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                   <a href="package-summary.html#Statelessness">stateless</a>
     *                   Comparator to compare elements of this stream
     * @return Optional an Optional describing the maximum element of this stream,
     * or an empty Optional if the stream is empty
     */
    function max(Comparator $comparator): Optional;

    /**
     * Returns the count of elements in this stream.  This is a special case of
     * a <a href="package-summary.html#Reduction">reduction</a> and is
     * equivalent to:
     * <pre>{@code
     *     return mapToLong(e -> 1L).sum();
     * }</pre>
     *
     * <p>This is a <a href="package-summary.html#StreamOps">terminal operation</a>.
     *
     * @apiNote
     * An implementation may choose to not execute the stream pipeline (either
     * sequentially or in parallel) if it is capable of computing the count
     * directly from the stream source.  In such cases no source elements will
     * be traversed and no intermediate operations will be evaluated.
     * Behavioral parameters with side-effects, which are strongly discouraged
     * except for harmless cases such as debugging, may be affected.  For
     * example, consider the following stream:
     * <pre>{@code
     *     List<String> l = Arrays.asList("A", "B", "C", "D");
     *     long count = l.stream().peek(System.out::println).count();
     * }</pre>
     * The number of elements covered by the stream source, a List, is
     * known and the intermediate operation, peek, does not inject into
     * or remove elements from the stream (as may be the case for
     * flatMap or filter operations).  Thus the count is the
     * size of the List and there is no need to execute the pipeline
     * and, as a side-effect, print out the list elements.
     *
     * @return int the count of elements in this stream
     */
    function count(): int;

    /**
     * Returns whether any elements of this stream match the provided
     * predicate.  May not evaluate the predicate on all elements if not
     * necessary for determining the result.  If the stream is empty then
     * false is returned and the predicate is not evaluated.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">short-circuiting
     * terminal operation</a>.
     *
     * @apiNote
     * This method evaluates the <em>existential quantification</em> of the
     * predicate over the elements of the stream (for some x P(x)).
     *
     * @param Predicate $predicate a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                  <a href="package-summary.html#Statelessness">stateless</a>
     *                  predicate to apply to elements of this stream
     * @return bool true if any elements of the stream match the provided
     * predicate, otherwise false
     */
    function anyMatch(Predicate $predicate): bool;

    /**
     * Returns whether all elements of this stream match the provided predicate.
     * May not evaluate the predicate on all elements if not necessary for
     * determining the result.  If the stream is empty then true is
     * returned and the predicate is not evaluated.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">short-circuiting
     * terminal operation</a>.
     *
     * @apiNote
     * This method evaluates the <em>universal quantification</em> of the
     * predicate over the elements of the stream (for all x P(x)).  If the
     * stream is empty, the quantification is said to be <em>vacuously
     * satisfied</em> and is always true (regardless of P(x)).
     *
     * @param Predicate $predicate a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                  <a href="package-summary.html#Statelessness">stateless</a>
     *                  predicate to apply to elements of this stream
     * @return bool true if either all elements of the stream match the
     * provided predicate or the stream is empty, otherwise false
     */
    function allMatch(Predicate $predicate): bool;

    /**
     * Returns whether no elements of this stream match the provided predicate.
     * May not evaluate the predicate on all elements if not necessary for
     * determining the result.  If the stream is empty then true is
     * returned and the predicate is not evaluated.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">short-circuiting
     * terminal operation</a>.
     *
     * @apiNote
     * This method evaluates the <em>universal quantification</em> of the
     * negated predicate over the elements of the stream (for all x ~P(x)).  If
     * the stream is empty, the quantification is said to be vacuously satisfied
     * and is always true, regardless of P(x).
     *
     * @param Predicate $predicate a <a href="package-summary.html#NonInterference">non-interfering</a>,
     *                  <a href="package-summary.html#Statelessness">stateless</a>
     *                  predicate to apply to elements of this stream
     * @return bool true if either no elements of the stream match the
     * provided predicate or the stream is empty, otherwise false
     */
    function noneMatch(Predicate $predicate): bool;

    /**
     * Returns an {@link Optional} describing the first element of this stream,
     * or an empty Optional if the stream is empty.  If the stream has
     * no encounter order, then any element may be returned.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">short-circuiting
     * terminal operation</a>.
     *
     * @return Optional an Optional describing the first element of this stream,
     * or an empty Optional if the stream is empty
     */
    function findFirst(): Optional;

    /**
     * Returns an {@link Optional} describing some element of the stream, or an
     * empty Optional if the stream is empty.
     *
     * <p>This is a <a href="package-summary.html#StreamOps">short-circuiting
     * terminal operation</a>.
     *
     * <p>The behavior of this operation is explicitly nondeterministic; it is
     * free to select any element in the stream.  This is to allow for maximal
     * performance in parallel operations; the cost is that multiple invocations
     * on the same source may not return the same result.  (If a stable result
     * is desired, use {@link #findFirst()} instead.)
     *
     * @return Optional an Optional describing some element of this stream, or an
     * empty Optional if the stream is empty
     * @see #findFirst()
     */
    function findAny(): Optional;
}