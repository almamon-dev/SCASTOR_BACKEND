import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import GuestLayout from "@/Layouts/GuestLayout";
import { Head, useForm } from "@inertiajs/react";

export default function Login({ status }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();

        post(route("login"), {
            onFinish: () => reset("password"),
        });
    };

    return (
        <GuestLayout>
            <Head title="Sign In | Admin" />

            <div className="mb-8">
                <h1 className="text-xl font-bold text-zinc-900 dark:text-zinc-50">
                    Sign In
                </h1>
                <p className="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Welcome back. Please enter your email and password to access the admin panel.
                </p>
            </div>

            {status && (
                <div className="mb-6 p-4 rounded-lg bg-green-50 border border-green-100 text-sm font-medium text-green-700 dark:bg-green-500/10 dark:text-green-400">
                    {status}
                </div>
            )}

            <form onSubmit={submit} className="space-y-6">
                <div>
                    <InputLabel htmlFor="email" value="Email" className="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5" />
                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="w-full text-sm border-zinc-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-lg dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100 transition-colors"
                        autoComplete="username"
                        placeholder="admin@example.com"
                        isFocused={true}
                        onChange={(e) => setData("email", e.target.value)}
                    />
                    <InputError message={errors.email} className="mt-1 text-xs" />
                </div>

                <div>
                    <InputLabel htmlFor="password" value="Password" className="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5" />
                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="w-full text-sm border-zinc-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-lg dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100 transition-colors"
                        autoComplete="current-password"
                        placeholder="••••••••"
                        onChange={(e) => setData("password", e.target.value)}
                    />
                    <InputError message={errors.password} className="mt-1 text-xs" />
                </div>

                <div className="pt-2">
                    <PrimaryButton 
                        className="w-full justify-center py-2.5 px-4 text-sm font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors border-none" 
                        disabled={processing}
                    >
                        {processing ? "Signing in..." : "Sign In"}
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
