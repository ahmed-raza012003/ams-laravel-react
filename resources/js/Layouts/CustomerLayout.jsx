import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    HomeIcon,
    DocumentTextIcon,
    CurrencyPoundIcon,
    ClipboardDocumentListIcon,
    CubeIcon,
    UserGroupIcon,
    Bars3Icon,
    XMarkIcon,
    ArrowRightOnRectangleIcon,
    UserCircleIcon,
    ReceiptPercentIcon,
} from '@heroicons/react/24/outline';

const navigation = [
    { name: 'Dashboard', href: '/customer/dashboard', icon: HomeIcon },
    { name: 'Customers', href: '/customer/customers', icon: UserGroupIcon },
    { name: 'Items', href: '/customer/items', icon: CubeIcon },
    { name: 'Invoices', href: '/customer/invoices', icon: DocumentTextIcon },
    { name: 'Estimates', href: '/customer/estimates', icon: ClipboardDocumentListIcon },
    { name: 'Expenses', href: '/customer/expenses', icon: CurrencyPoundIcon },
    { name: 'Tax Types', href: '/customer/tax-types', icon: ReceiptPercentIcon },
];

export default function CustomerLayout({ children, title }) {
    const { auth } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);

    const currentPath = window.location.pathname;

    return (
        <div className="min-h-screen bg-gray-50">
            <div
                className={`fixed inset-0 z-40 bg-gray-900/80 lg:hidden ${sidebarOpen ? 'block' : 'hidden'}`}
                onClick={() => setSidebarOpen(false)}
            />

            <aside
                className={`fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 lg:translate-x-0 ${
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                <div className="flex h-16 items-center justify-between px-6 border-b border-gray-200">
                    <Link href="/customer/dashboard" className="flex items-center space-x-2">
                        <div className="w-8 h-8 bg-[#2ca48b] rounded-lg flex items-center justify-center">
                            <CurrencyPoundIcon className="w-5 h-5 text-white" />
                        </div>
                        <span className="text-xl font-bold text-gray-900">FinanceFlow</span>
                    </Link>
                    <button
                        className="lg:hidden text-gray-500 hover:text-gray-700"
                        onClick={() => setSidebarOpen(false)}
                    >
                        <XMarkIcon className="w-6 h-6" />
                    </button>
                </div>

                <div className="px-3 py-4">
                    <div className="mb-4 px-3">
                        <span className="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Customer Portal
                        </span>
                    </div>
                    <nav className="space-y-1">
                        {navigation.map((item) => {
                            const isActive = currentPath === item.href || currentPath.startsWith(item.href + '/');
                            return (
                                <Link
                                    key={item.name}
                                    href={item.href}
                                    className={`flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors ${
                                        isActive
                                            ? 'bg-[#2ca48b] text-white'
                                            : 'text-gray-700 hover:bg-gray-100'
                                    }`}
                                >
                                    <item.icon className={`w-5 h-5 mr-3 ${isActive ? 'text-white' : 'text-gray-400'}`} />
                                    {item.name}
                                </Link>
                            );
                        })}
                    </nav>
                </div>

                <div className="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
                    <div className="flex items-center space-x-3 mb-3">
                        <div className="w-10 h-10 bg-[#2ca48b] rounded-full flex items-center justify-center">
                            <span className="text-white font-medium">
                                {auth.user?.name?.charAt(0).toUpperCase()}
                            </span>
                        </div>
                        <div className="flex-1 min-w-0">
                            <p className="text-sm font-medium text-gray-900 truncate">{auth.user?.name}</p>
                            <p className="text-xs text-gray-500 truncate">{auth.user?.email}</p>
                        </div>
                    </div>
                    <div className="flex space-x-2">
                        <Link
                            href="/profile"
                            className="flex-1 flex items-center justify-center px-3 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                        >
                            <UserCircleIcon className="w-4 h-4 mr-1" />
                            Profile
                        </Link>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            className="flex-1 flex items-center justify-center px-3 py-2 text-sm text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
                        >
                            <ArrowRightOnRectangleIcon className="w-4 h-4 mr-1" />
                            Logout
                        </Link>
                    </div>
                </div>
            </aside>

            <div className="lg:pl-64">
                <header className="sticky top-0 z-30 bg-white border-b border-gray-200">
                    <div className="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                        <button
                            className="lg:hidden text-gray-500 hover:text-gray-700"
                            onClick={() => setSidebarOpen(true)}
                        >
                            <Bars3Icon className="w-6 h-6" />
                        </button>
                        <h1 className="text-xl font-semibold text-gray-900">{title}</h1>
                        <div className="flex items-center space-x-4">
                            <span className="text-sm text-gray-500">Customer</span>
                        </div>
                    </div>
                </header>

                <main className="p-4 sm:p-6 lg:p-8">{children}</main>
            </div>
        </div>
    );
}
