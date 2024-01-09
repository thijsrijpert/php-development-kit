<?php

namespace jhp\lang;

use jhp\lang\exception\CloneNotSupportedException;
use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\util\function\internal\NullPointerException;

/**
 * This is the common base class of all Java language enumeration classes.
 *
 * Enumeration classes are all serializable and receive special handling
 * by the serialization mechanism. The serialized representation used
 * for enum constants cannot be customized. Declarations of methods
 * and fields that would otherwise interact with serialization are
 * ignored, including serialVersionUID; see the <cite>Java
 * Object Serialization Specification</cite> for details.
 *
 * <p> Note that when using an enumeration type as the type of set
 * or as the type of the keys in a map, specialized and efficient
 * {@linkplain java.util.EnumSet set} and {@linkplain
 * java.util.EnumMap map} implementations are available.
 *
 * @param <E> The type of the enum subclass
 *
 * @serial exclude
 * @see     Class#getEnumConstants()
 * @see     java.util.EnumSet
 * @see     java.util.EnumMap
 */
trait EnumTrait
{

    /**
     * Returns the name of this enum constant, exactly as declared in its
     * enum declaration.
     *
     * <b>Most programmers should use the {@link #toString} method in
     * preference to this one, as the toString method may return
     * a more user-friendly name.</b>  This method is designed primarily for
     * use in specialized situations where correctness depends on getting the
     * exact name, which will not vary from release to release.
     *
     * @return the name of this enum constant
     */
    final public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns the ordinal of this enumeration constant (its position
     * in its enum declaration, where the initial constant is assigned
     * an ordinal of zero).
     *
     * Most programmers will have no use for this method.  It is
     * designed for use by sophisticated enum-based data structures, such
     * as {@link java.util.EnumSet} and {@link java.util.EnumMap}.
     *
     * @return the ordinal of this enumeration constant
     */
    final public function ordinal(): int
    {
        foreach (self::cases() as $key => $value) {
            if ($value === $this) {
                return $key;
            }
        }

        return -1;
    }

    /**
     * Returns the name of this enum constant, as contained in the
     * declaration.  This method may be overridden, though it typically
     * isn't necessary or desirable.  An enum class should override this
     * method when a more "programmer-friendly" string form exists.
     *
     * @return the name of this enum constant
     */
    public function toString(): string
    {
        return $this->name;
    }

    /**
     * Returns true if the specified object is equal to this
     * enum constant.
     *
     * @param other the object to be compared for equality with this object.
     *
     * @return  true if the specified object is equal to this
     *          enum constant.
     */
    final public function equals(?IObject $other = null): bool
    {
        if ($other === null) {
            return false;
        }
        return $this === $other;
    }

    /**
     * Returns the runtime class of this TObject.
     *
     * @return TClass The class object that represents the runtime class of this object.
     */
    public function getClass(): TClass
    {
        return TClass::of($this);
    }

    /**
     * Returns a hash code for this enum constant.
     *
     * @return a hash code for this enum constant.
     */
    public final function hashCode(): int
    {
        return System::identityHashCode($this);
    }

    /**
     * Throws CloneNotSupportedException.  This guarantees that enums
     * are never cloned, which is necessary to preserve their "singleton"
     * status.
     *
     * @return (never returns)
     * @throws CloneNotSupportedException
     */
    public function clone(): TObject
    {
        throw new CloneNotSupportedException();
    }


    /**
     * Compares this enum with the specified object for order.  Returns a
     * negative integer, zero, or a positive integer as this object is less
     * than, equal to, or greater than the specified object.
     *
     * Enum constants are only comparable to other enum constants of the
     * same enum type.  The natural order implemented by this
     * method is the order in which the constants are declared.
     */
    public final function compareTo(IObject $obj): int
    {
        if ($obj instanceof IEnum && $this->getClass()->equals($obj->getClass())) {
            return $this->ordinal() - $obj->ordinal();
        }

        throw new IllegalArgumentException();
    }

    /**
     * Returns the Class object corresponding to this enum constant's
     * enum type.  Two enum constants e1 and  e2 are of the
     * same enum type if and only if
     *   e1.getDeclaringClass() == e2.getDeclaringClass().
     * (The value returned by this method may differ from the one returned
     * by the {@link Object#getClass} method for enum constants with
     * constant-specific class bodies.)
     *
     * @return the Class object corresponding to this enum constant's
     *     enum type
     */
    public final function getDeclaringClass(): TClass
    {
        return $this->getClass();
    }

    /**
     * Returns the enum constant of the specified enum class with the
     * specified name.  The name must match exactly an identifier used
     * to declare an enum constant in this class.  (Extraneous whitespace
     * characters are not permitted.)
     *
     * <p>Note that for a particular enum class {@code T}, the
     * implicitly declared {@code public static T valueOf(String)}
     * method on that enum may be used instead of this method to map
     * from a name to the corresponding enum constant.  All the
     * constants of an enum class can be obtained by calling the
     * implicit {@code public static T[] values()} method of that
     * class.
     *
     * @param <T> The enum class whose constant is to be returned
     * @param enumClass the {@code Class} object of the enum class from which
     *      to return a constant
     * @param name the name of the constant to return
     *
     * @return the enum constant of the specified enum class with the
     *      specified name
     *
     * @throws IllegalArgumentException if the specified enum class has
     *         no constant with the specified name, or the specified
     *         class object does not represent an enum class
     * @throws NullPointerException if {@code enumClass} or {@code name}
     *         is null
     * @since 1.5
     */
    public static function valueOf(TClass $enumClass, string $name): IEnum
    {
        $result = $enumClass->enumConstantDirectory()->get($name);

        if ($result === null) {
            throw new IllegalArgumentException("No enum constant " . $enumClass->getCanonicalName() . "." . $name);
        }

        return $result;
    }

    /**
     * Wakes up a single thread that is waiting on this object's
     * monitor. If any threads are waiting on this object, one of them
     * is chosen to be awakened. The choice is arbitrary and occurs at
     * the discretion of the implementation. A thread waits on an object's
     * monitor by calling one of the wait methods.
     * <p>
     * The awakened thread will not be able to proceed until the current
     * thread relinquishes the lock on this object. The awakened thread will
     * compete in the usual manner with any other threads that might be
     * actively competing to synchronize on this object; for example, the
     * awakened thread enjoys no reliable privilege or disadvantage in being
     * the next thread to lock this object.
     * <p>
     * This method should only be called by a thread that is the owner
     * of this object's monitor. A thread becomes the owner of the
     * object's monitor in one of three ways:
     * <ul>
     * <li>By executing a synchronized instance method of that object.
     * <li>By executing the body of a synchronized statement
     *     that synchronizes on the object.
     * <li>For objects of type Class, by executing a
     *     synchronized static method of that class.
     * </ul>
     * <p>
     * Only one thread at a time can own an object's monitor.
     *
     * @throws  IllegalMonitorStateException  if the current thread is not
     *               the owner of this object's monitor.
     * @see        java.lang.Object#notifyAll()
     * @see        java.lang.Object#wait()
     */
    public final function notify(): void
    {
        throw new UnsupportedOperationException();
    }

    /**
     * Wakes up all threads that are waiting on this object's monitor. A
     * thread waits on an object's monitor by calling one of the
     * wait methods.
     * <p>
     * The awakened threads will not be able to proceed until the current
     * thread relinquishes the lock on this object. The awakened threads
     * will compete in the usual manner with any other threads that might
     * be actively competing to synchronize on this object; for example,
     * the awakened threads enjoy no reliable privilege or disadvantage in
     * being the next thread to lock this object.
     * <p>
     * This method should only be called by a thread that is the owner
     * of this object's monitor. See the notify method for a
     * description of the ways in which a thread can become the owner of
     * a monitor.
     *
     * @throws  IllegalMonitorStateException  if the current thread is not
     *               the owner of this object's monitor.
     * @see        java.lang.Object#notify()
     * @see        java.lang.Object#wait()
     */
    public final function notifyAll(): void
    {
        throw new UnsupportedOperationException();
    }

    /**
     * Causes the current thread to wait until it is awakened, typically
     * by being <em>notified</em> or <em>interrupted</em>.
     *
     * @throws IllegalMonitorStateException if the current thread is not
     *         the owner of the object's monitor
     * @throws InterruptedException if any thread interrupted the current thread before or
     *         while the current thread was waiting. The <em>interrupted status</em> of the
     *         current thread is cleared when this exception is thrown.
     * @see    #notify()
     * @see    #notifyAll()
     */
    public final function wait(int $timeout = 0, int $nanos = 0): void
    {
        throw new UnsupportedOperationException();
    }
}