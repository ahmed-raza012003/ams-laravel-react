import AdminLayout from '@/Layouts/AdminLayout';
import CustomerLayout from '@/Layouts/CustomerLayout';
import { Head } from '@inertiajs/react';
import DeleteUserForm from './Partials/DeleteUserForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';

export default function Edit({ mustVerifyEmail, status, role }) {
    const Layout = role === 'admin' ? AdminLayout : CustomerLayout;

    return (
        <Layout title="Profile">
            <Head title="Profile" />

            <div className="space-y-6">
                <div className="bg-white rounded-lg shadow-sm p-6">
                    <UpdateProfileInformationForm
                        mustVerifyEmail={mustVerifyEmail}
                        status={status}
                        className="max-w-xl"
                    />
                </div>

                <div className="bg-white rounded-lg shadow-sm p-6">
                    <UpdatePasswordForm className="max-w-xl" />
                </div>

                {role === 'admin' && (
                    <div className="bg-white rounded-lg shadow-sm p-6">
                        <DeleteUserForm className="max-w-xl" />
                    </div>
                )}
            </div>
        </Layout>
    );
}
