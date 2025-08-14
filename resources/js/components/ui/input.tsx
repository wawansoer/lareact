import * as React from 'react';
import { Eye, EyeOff } from 'lucide-react';
import { cn } from '@/lib/utils';

// --- Base Input Component ---

const inputBaseClasses =
  "file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm";

const inputFocusClasses =
  "focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]";

const inputInvalidClasses =
  "aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive";

export type InputProps = React.InputHTMLAttributes<HTMLInputElement>;

const Input = React.memo(
  React.forwardRef<HTMLInputElement, InputProps>(
    ({ className, type, ...props }, ref) => {
      return (
        <input
          type={type}
          className={cn(
            inputBaseClasses,
            inputFocusClasses,
            inputInvalidClasses,
            className,
          )}
          ref={ref}
          {...props}
        />
      );
    },
  ),
);
Input.displayName = 'Input';

// --- Password Input Component ---

const PasswordInput = React.memo(
  React.forwardRef<HTMLInputElement, InputProps>((props, ref) => {
    const [showPassword, setShowPassword] = React.useState(false);

    const togglePasswordVisibility = React.useCallback(() => {
      setShowPassword((prev) => !prev);
    }, []);

    const Icon = showPassword ? EyeOff : Eye;

    return (
      <div className="relative">
        <Input
          type={showPassword ? 'text' : 'password'}
          className="pr-10"
          ref={ref}
          {...props}
        />
        <button
          type="button"
          className="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-foreground"
          onClick={togglePasswordVisibility}
          aria-label={showPassword ? 'Hide password' : 'Show password'}
        >
          <Icon className="h-5 w-5" />
        </button>
      </div>
    );
  }),
);
PasswordInput.displayName = 'PasswordInput';

export { Input, PasswordInput };
