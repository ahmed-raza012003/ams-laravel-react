import { Head, Link } from '@inertiajs/react';
import {
    CurrencyPoundIcon,
    DocumentTextIcon,
    ChartBarIcon,
    ShieldCheckIcon,
    UsersIcon,
    ClipboardDocumentListIcon,
    ArrowTrendingUpIcon,
    Cog6ToothIcon,
} from '@heroicons/react/24/outline';

export default function Welcome({ canLogin, canRegister, companyName }) {
    return (
        <>
            <Head title="Welcome" />
            <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
                <header className="bg-white shadow-sm">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between items-center h-16">
                            <div className="flex items-center space-x-2">
                                <div className="w-10 h-10 bg-[#2ca48b] rounded-xl flex items-center justify-center">
                                    <CurrencyPoundIcon className="w-6 h-6 text-white" />
                                </div>
                                <span className="text-2xl font-bold text-gray-900">{companyName || 'FinanceFlow'}</span>
                            </div>
                            <nav className="flex items-center space-x-4">
                                {canLogin && (
                                    <>
                                        <Link href="/login" className="text-gray-600 hover:text-[#2ca48b] font-medium transition-colors">Log in</Link>
                                        {canRegister && (
                                            <Link href="/register" className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] transition-colors">Get Started</Link>
                                        )}
                                    </>
                                )}
                            </nav>
                        </div>
                    </div>
                </header>

                <main>
                    <section className="py-20">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            <h1 className="text-5xl font-bold text-gray-900 mb-6">
                                Financial Management <span className="text-[#2ca48b]">Made Simple</span>
                            </h1>
                            <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                                A comprehensive accounting management solution for tracking expenses, revenue, invoices, and financial analytics. Built for businesses that need clarity and control.
                            </p>
                            <div className="flex justify-center space-x-4">
                                <Link href="/register" className="px-8 py-3 bg-[#2ca48b] text-white text-lg font-medium rounded-xl hover:bg-[#238b74] transition-colors shadow-lg shadow-[#2ca48b]/25">
                                    Start Free Trial
                                </Link>
                                <Link href="/login" className="px-8 py-3 bg-white text-gray-700 text-lg font-medium rounded-xl hover:bg-gray-50 transition-colors border border-gray-200">
                                    Sign In
                                </Link>
                            </div>
                        </div>
                    </section>

                    <section className="py-16 bg-white">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div className="text-center mb-12">
                                <h2 className="text-3xl font-bold text-gray-900 mb-4">Project Overview</h2>
                                <p className="text-gray-600 max-w-2xl mx-auto">Everything you need to manage your business finances in one place</p>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                                {[
                                    { icon: DocumentTextIcon, title: 'Invoicing', desc: 'Create and manage professional invoices' },
                                    { icon: CurrencyPoundIcon, title: 'Expense Tracking', desc: 'Track all business expenses' },
                                    { icon: ChartBarIcon, title: 'Analytics', desc: 'Visual financial insights' },
                                    { icon: ArrowTrendingUpIcon, title: 'Profit/Loss', desc: 'Real-time P&L calculations' },
                                ].map((item, i) => (
                                    <div key={i} className="text-center p-6 rounded-xl bg-gray-50 hover:bg-[#2ca48b]/5 transition-colors">
                                        <div className="w-14 h-14 bg-[#2ca48b]/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                                            <item.icon className="w-7 h-7 text-[#2ca48b]" />
                                        </div>
                                        <h3 className="text-lg font-semibold text-gray-900 mb-2">{item.title}</h3>
                                        <p className="text-gray-600">{item.desc}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    <section className="py-16">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div className="text-center mb-12">
                                <h2 className="text-3xl font-bold text-gray-900 mb-4">Portal Features</h2>
                                <p className="text-gray-600 max-w-2xl mx-auto">Powerful tools for both customers and administrators</p>
                            </div>
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                                    <div className="flex items-center mb-6">
                                        <div className="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                            <UsersIcon className="w-6 h-6 text-blue-600" />
                                        </div>
                                        <h3 className="text-2xl font-bold text-gray-900">Customer Portal</h3>
                                    </div>
                                    <ul className="space-y-3">
                                        {['Login / Signup', 'Expense Records', 'Revenue Records', 'Reconciliation', 'Reports', 'Analytics', 'Exporting', 'Responsive Web-App UI'].map((item, i) => (
                                            <li key={i} className="flex items-center text-gray-600">
                                                <span className="w-2 h-2 bg-[#2ca48b] rounded-full mr-3"></span>{item}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                                <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                                    <div className="flex items-center mb-6">
                                        <div className="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                                            <ShieldCheckIcon className="w-6 h-6 text-purple-600" />
                                        </div>
                                        <h3 className="text-2xl font-bold text-gray-900">Admin Portal</h3>
                                    </div>
                                    <ul className="space-y-3">
                                        {['Analytics Dashboard', 'User Management', 'System Settings', 'Financial Overviews', 'Reports', 'Global Access to all modules'].map((item, i) => (
                                            <li key={i} className="flex items-center text-gray-600">
                                                <span className="w-2 h-2 bg-purple-500 rounded-full mr-3"></span>{item}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section className="py-16 bg-white">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div className="text-center mb-12">
                                <h2 className="text-3xl font-bold text-gray-900 mb-4">Admin Features</h2>
                                <p className="text-gray-600 max-w-2xl mx-auto">Full control over your financial operations</p>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {[
                                    { icon: ClipboardDocumentListIcon, title: 'Invoices & Estimates', desc: 'Create, edit, and manage all invoices and quotes with line items' },
                                    { icon: UsersIcon, title: 'Customer Management', desc: 'Maintain customer database with contact details and history' },
                                    { icon: Cog6ToothIcon, title: 'System Control', desc: 'Configure settings, manage users, and control access levels' },
                                ].map((item, i) => (
                                    <div key={i} className="p-6 bg-gradient-to-br from-[#2ca48b]/5 to-[#2ca48b]/10 rounded-xl">
                                        <item.icon className="w-10 h-10 text-[#2ca48b] mb-4" />
                                        <h3 className="text-lg font-semibold text-gray-900 mb-2">{item.title}</h3>
                                        <p className="text-gray-600">{item.desc}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                </main>

                <footer className="bg-gray-900 text-white py-12">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex flex-col md:flex-row justify-between items-center">
                            <div className="flex items-center space-x-2 mb-4 md:mb-0">
                                <div className="w-8 h-8 bg-[#2ca48b] rounded-lg flex items-center justify-center">
                                    <CurrencyPoundIcon className="w-5 h-5 text-white" />
                                </div>
                                <span className="text-xl font-bold">{companyName || 'FinanceFlow'}</span>
                            </div>
                            <p className="text-gray-400">Financial Accounting Management System</p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
