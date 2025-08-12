import AppLogoIcon from '@/components/app-logo-icon';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Link } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

export default function AuthCardLayout({
  children,
  title,
  description,
}: PropsWithChildren<{
  name?: string;
  title?: string;
  description?: string;
}>) {
  return (
    <div className="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
      <div className="flex w-full max-w-md flex-col gap-6">
        <div className="flex flex-col gap-6">
          <Card className="w-full max-w-md rounded-xl border border-border py-12 shadow-lg">
            <CardHeader className="space-y-2 px-6 text-center">
              <Link href={route('home')} className="mb-4 flex items-center justify-center gap-2">
                <div className="flex h-auto w-auto items-center justify-center rounded-lg bg-muted">
                  <AppLogoIcon className="size-18 fill-current text-primary" />
                </div>
              </Link>
              <CardTitle className="text-xl leading-tight font-semibold">{title}</CardTitle>
              <CardDescription className="text-sm text-muted-foreground">{description}</CardDescription>
            </CardHeader>
            <CardContent className="px-6">{children}</CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
