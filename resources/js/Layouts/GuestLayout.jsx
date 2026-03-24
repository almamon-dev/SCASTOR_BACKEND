import { Link } from "@inertiajs/react";

export default function GuestLayout({ children }) {
    return (
        <div className="flex min-h-screen items-center justify-center bg-zinc-50 dark:bg-zinc-950 p-6">
            <div className="w-full sm:max-w-[400px]">
                <div className="bg-white px-8 py-10 shadow-sm border border-zinc-200 sm:rounded-2xl dark:bg-zinc-900/50 dark:border-zinc-800">
                    {children}
                </div>
            </div>
        </div>
    );
}
