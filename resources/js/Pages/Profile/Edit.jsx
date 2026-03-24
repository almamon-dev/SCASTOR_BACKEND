import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import DeleteUserForm from './Partials/DeleteUserForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';

export default function Edit({ mustVerifyEmail, status }) {
    return (
        <AdminLayout>
            <Head title="Profile" />

            <div className="space-y-6">
                <div className="mb-6">
                    <h2 className="text-2xl font-bold text-slate-800 tracking-tight">
                        Profile Settings
                    </h2>
                    <p className="text-sm text-slate-500 mt-1">
                        Update your account's profile information and email address.
                    </p>
                </div>

                <div className="bg-white p-6 shadow-sm border border-slate-100 rounded-xl sm:p-8">
                    <UpdateProfileInformationForm
                        mustVerifyEmail={mustVerifyEmail}
                        status={status}
                        className="max-w-xl"
                    />
                </div>

                <div className="bg-white p-6 shadow-sm border border-slate-100 rounded-xl sm:p-8">
                    <UpdatePasswordForm className="max-w-xl" />
                </div>

                <div className="bg-white p-6 shadow-sm border border-slate-100 rounded-xl sm:p-8">
                    <DeleteUserForm className="max-w-xl" />
                </div>
            </div>
        </AdminLayout>
    );
}
