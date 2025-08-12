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
        <div className="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10 bg-background">
            <div className="flex w-full max-w-md flex-col gap-6">
                <div className="flex flex-col gap-6">
                    <Card className="rounded-xl shadow-lg border border-border max-w-md w-full py-12">
                        <CardHeader className="px-6 text-center space-y-2">
                            <Link href={route('home')} className="flex items-center justify-center gap-2 mb-4">
                                <div className="flex h-auto w-auto items-center justify-center rounded-lg bg-muted">
                                    <AppLogoIcon className="size-18 fill-current text-primary" />
                                </div>
                            </Link>
                            <CardTitle className="text-xl font-semibold leading-tight">
                                {title}
                            </CardTitle>
                            <CardDescription className="text-muted-foreground text-sm">{description}</CardDescription>
                        </CardHeader>
                        <CardContent className="px-6">{children}</CardContent>
                    </Card>
                </div>
            </div>
        </div>
    );
}
