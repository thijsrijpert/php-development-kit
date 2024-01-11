# php-development-kit
The PHP Development Kit is a project to port the core interfaces and classes of OpenJDK to PHP.

Project is a Work in Progress and should not be used in production for now.

## Standards

The PHP Developement Kit conforms to the following standards:

- PSR-1 - Basic coding standard (http://www.php-fig.org/psr/psr-1/)
- PSR-4 - Autoloader (http://www.php-fig.org/psr/psr-4/)
- PSR-12 - Extended coding style guide (http://www.php-fig.org/psr/psr-12/)

## Usage
All classes that interact with this library should extend TObject or implement IObject

All enums that interact with this library should implement IObject and use the EnumTrait, which makes it compatible with IObject

### Object Example (TBoolean, Shortend)

    class TBoolean extends TObject implements Serializable, Comparable
    {
    
        /**
         * Create a new boolean wrapper object
         * @param bool $value the value being wrapped
         */
        public function __construct(private readonly bool $value) { }
  
        /**
         * Returns the value of this Boolean object as a boolean
         * primitive.
         *
         * @return  bool the primitive boolean value of this object.
         */
        public function booleanValue(): bool {
            return $this->value;
        }
    
        /**
         * Returns a Boolean instance representing the specified
         * boolean value.  If the specified boolean value
         * is true, this method returns Boolean.TRUE;
         * if it is false, this method returns Boolean.FALSE.
         * If a new Boolean instance is not required, this method
         * should generally be used in preference to the constructor
         * {@link #Boolean(boolean)}, as this method is likely to yield
         * significantly better space and time performance.
         *
         * @param  bool|string $b a boolean value.
         * @return TBoolean a Boolean instance representing b.
         * @since  1.4
         */
        public static function valueOf(bool|string $b): TBoolean {
            if (GType::of($b)->isString()) {
                $b = self::parseBoolean($b);
            }
            return $b ? new TBoolean(true) : new TBoolean(false);
        }
        
        /**
        * Convert the internal value to a string representation
        * @return string the string representation of the internal value
        */
        public function toString(): string {
            return $b ? "true" : "false";
        }
    
        /**
         * Returns a hash code for this Boolean object.
         *
         * @return int the integer 1231 if this object represents
         * true; returns the integer 1237 if this
         * object represents false.
         */
        public function hashCode(): int {
            return $value ? 1231 : 1237;
        }
    
        /**
         * Returns true if and only if the argument is not
         * null and is a Boolean object that
         * represents the same boolean value as this object.
         *
         * @param   ?IObject $obj   the object to compare with.
         * @return  bool true if the Boolean objects represent the
         *          same value; false otherwise.
         */
        public function equals(?IObject $obj = null): bool {
            if ($obj === null) {
                return false;
            }
            if ($obj instanceof TBoolean) {
                return $this->value == ($obj->booleanValue());
            }
            return false;
        }
    }

### Enum Example (GType, Shortend)

    enum GType implements IEnum
    {

        use EnumTrait;

        case BOOLEAN;
        case INTEGER;
        case FLOAT;
        case STRING;
        case ARRAY;
        case OBJECT;
        case RESOURCE;
        case RESOURCE_CLOSED;
        case NULL;
        case UNKNOWN;

        /*
        * Instance Methods
        */

        /*
        * Class methods
        */
    }


## Testing

All code should be unit tested before release, the goal is to have 100% test coverage.

Testing is enforced by the pipeline

## Licenses 
This repository uses GPLv2 with class path exception for more information see
- LICENSE
- ADDITIONAL_LICENSE_INFO

