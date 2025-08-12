import { Button } from '@/components/ui/button';

export default function GoogleSignInButton() {
  return (
    <>
      <div className="relative">
        <div className="absolute inset-0 flex items-center">
          <span className="w-full border-t" />
        </div>
        <div className="relative flex justify-center text-xs uppercase">
          <span className="bg-card px-2 text-muted-foreground">Or continue with</span>
        </div>
      </div>

      <Button variant="outline" asChild>
        <a href={route('google.redirect')}>
          <svg className="mr-2 h-4 w-4" viewBox="0 0 24 24">
            <path
              fill="currentColor"
              d="M21.35,11.1H12.18V13.83H18.69C18.36,17.64 15.19,19.27 12.19,19.27C8.36,19.27 5,16.25 5,12C5,7.9 8.2,4.73 12.19,4.73C14.76,4.73 16.04,5.7 17.22,6.88L19.36,4.74C17.22,2.77 15,2 12.19,2C6.92,2 3,6.5 3,12C3,17.5 6.92,22 12.19,22C17.6,22 21.7,18.35 21.7,12.33C21.7,11.7 21.52,11.4 21.35,11.1V11.1Z"
            />
          </svg>
          Google
        </a>
      </Button>
    </>
  );
}
