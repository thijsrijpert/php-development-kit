<?php
namespace jhp\lang;

use jhp\io\Serializable;

/**
 * The {@code Character} class wraps a value of the primitive
 * type {@code char} in an object. An object of class
 * {@code Character} contains a single field whose type is
 * {@code char}.
 * <p>
 * In addition, this class provides a large number of static methods for
 * determining a character's category (lowercase letter, digit, etc.)
 * and for converting characters from uppercase to lowercase and vice
 * versa.
 *
 * <h3><a id="conformance">Unicode Conformance</a></h3>
 * <p>
 * The fields and methods of class {@code Character} are defined in terms
 * of character information from the Unicode Standard, specifically the
 * <i>UnicodeData</i> file that is part of the Unicode Character Database.
 * This file specifies properties including name and category for every
 * assigned Unicode code point or character range. The file is available
 * from the Unicode Consortium at
 * <a href="http://www.unicode.org">http://www.unicode.org</a>.
 * <p>
 * The Java SE 8 Platform uses character information from version 6.2
 * of the Unicode Standard, with three extensions. First, in recognition of the fact
 * that new currencies appear frequently, the Java SE 8 Platform allows an
 * implementation of class {@code Character} to use the Currency Symbols
 * block from version 10.0 of the Unicode Standard. Second, the Java SE 8 Platform
 * allows an implementation of class {@code Character} to use the code points
 * in the range of {@code U+9FCD} to {@code U+9FEF} from version 11.0 of the
 * Unicode Standard and in the {@code CJK Unified Ideographs Extension E} block
 * from version 8.0 of the Unicode Standard, in order for the class to allow the
 * "Implementation Level 2" of the Chinese GB18030-2022 standard.
 * Third, the Java SE 8 Platform
 * allows an implementation of class {@code Character} to use the Japanese Era
 * code point, {@code U+32FF}, from the Unicode Standard version 12.1.
 * Consequently, the
 * behavior of fields and methods of class {@code Character} may vary across
 * implementations of the Java SE 8 Platform when processing the aforementioned
 * code points ( outside of version 6.2 ), except for the following methods
 * that define Java identifiers:
 * {@link #isJavaIdentifierStart(int)}, {@link #isJavaIdentifierStart(char)},
 * {@link #isJavaIdentifierPart(int)}, and {@link #isJavaIdentifierPart(char)}.
 * Code points in Java identifiers must be drawn from version 6.2 of
 * the Unicode Standard.
 *
 * <h3><a name="unicode">Unicode Character Representations</a></h3>
 *
 * <p>The {@code char} data type (and therefore the value that a
 * {@code Character} object encapsulates) are based on the
 * original Unicode specification, which defined characters as
 * fixed-width 16-bit entities. The Unicode Standard has since been
 * changed to allow for characters whose representation requires more
 * than 16 bits.  The range of legal <em>code point</em>s is now
 * U+0000 to U+10FFFF, known as <em>Unicode scalar value</em>.
 * (Refer to the <a
 * href="http://www.unicode.org/reports/tr27/#notation"><i>
 * definition</i></a> of the U+<i>n</i> notation in the Unicode
 * Standard.)
 *
 * <p><a name="BMP">The set of characters from U+0000 to U+FFFF</a> is
 * sometimes referred to as the <em>Basic Multilingual Plane (BMP)</em>.
 * <a name="supplementary">Characters</a> whose code points are greater
 * than U+FFFF are called <em>supplementary character</em>s.  The Java
 * platform uses the UTF-16 representation in {@code char} arrays and
 * in the {@code String} and {@code StringBuffer} classes. In
 * this representation, supplementary characters are represented as a pair
 * of {@code char} values, the first from the <em>high-surrogates</em>
 * range, (&#92;uD800-&#92;uDBFF), the second from the
 * <em>low-surrogates</em> range (&#92;uDC00-&#92;uDFFF).
 *
 * <p>A {@code char} value, therefore, represents Basic
 * Multilingual Plane (BMP) code points, including the surrogate
 * code points, or code units of the UTF-16 encoding. An
 * {@code int} value represents all Unicode code points,
 * including supplementary code points. The lower (least significant)
 * 21 bits of {@code int} are used to represent Unicode code
 * points and the upper (most significant) 11 bits must be zero.
 * Unless otherwise specified, the behavior with respect to
 * supplementary characters and surrogate {@code char} values is
 * as follows:
 *
 * <ul>
 * <li>The methods that only accept a {@code char} value cannot support
 * supplementary characters. They treat {@code char} values from the
 * surrogate ranges as undefined characters. For example,
 * {@code Character.isLetter('\u005CuD840')} returns {@code false}, even though
 * this specific value if followed by any low-surrogate value in a string
 * would represent a letter.
 *
 * <li>The methods that accept an {@code int} value support all
 * Unicode characters, including supplementary characters. For
 * example, {@code Character.isLetter(0x2F81A)} returns
 * {@code true} because the code point value represents a letter
 * (a CJK ideograph).
 * </ul>
 *
 * <p>In the Java SE API documentation, <em>Unicode code point</em> is
 * used for character values in the range between U+0000 and U+10FFFF,
 * and <em>Unicode code unit</em> is used for 16-bit
 * {@code char} values that are code units of the <em>UTF-16</em>
 * encoding. For more information on Unicode terminology, refer to the
 * <a href="http://www.unicode.org/glossary/">Unicode Glossary</a>.
 *
 * @author  Lee Boynton
 * @author  Guy Steele
 * @author  Akira Tanaka
 * @author  Martin Buchholz
 * @author  Ulf Zibis
 * @since   1.0
 */
class Character implements Serializable, Comparable
{
    /**
     * The minimum radix available for conversion to and from strings.
     * The constant value of this field is the smallest value permitted
     * for the radix argument in radix-conversion methods such as the
     * {@code digit} method, the {@code forDigit} method, and the
     * {@code toString} method of class {@code Integer}.
     *
     * @see     Character#digit(char, int)
     * @see     Character#forDigit(int, int)
     * @see     Integer#toString(int, int)
     * @see     Integer#valueOf(String)
     */
    public const MIN_RADIX = 2;

    /**
     * The maximum radix available for conversion to and from strings.
     * The constant value of this field is the largest value permitted
     * for the radix argument in radix-conversion methods such as the
     * {@code digit} method, the {@code forDigit} method, and the
     * {@code toString} method of class {@code Integer}.
     *
     * @see     Character#digit(char, int)
     * @see     Character#forDigit(int, int)
     * @see     Integer#toString(int, int)
     * @see     Integer#valueOf(String)
     */
    public const MAX_RADIX = 36;

}