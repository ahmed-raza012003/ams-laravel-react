import { Head } from '@inertiajs/react';
import CustomerLayout from '@/Layouts/CustomerLayout';
import StatCard from '@/Components/StatCard';
import {
    DocumentTextIcon,
    CurrencyPoundIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    UserGroupIcon,
    ClipboardDocumentListIcon,
} from '@heroicons/react/24/outline';

export default function Dashboard({ stats, recentActivity, currency }) {
    const formatCurrency = (amount) => {
        if (amount == null || isNaN(amount)) return `${currency}0.00`;
        const num = Number(amount);
        return isNaN(num) ? `${currency}0.00` : `${currency}${num.toFixed(2)}`;
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? '-' : date.toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    };

    const getStatusBadge = (status, type) => {
        const statusColors = {
            DRAFT: 'bg-gray-100 text-gray-700',
            SENT: 'bg-blue-100 text-blue-700',
            PAID: 'bg-green-100 text-green-700',
            OVERDUE: 'bg-red-100 text-red-700',
            CANCELLED: 'bg-gray-100 text-gray-500',
            ACCEPTED: 'bg-green-100 text-green-700',
            REJECTED: 'bg-red-100 text-red-700',
            EXPIRED: 'bg-yellow-100 text-yellow-700',
        };
        return (
            <span className={`inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[status] || 'bg-gray-100 text-gray-700'}`}>
                {status}
            </span>
        );
    };

    return (
        <CustomerLayout title="Dashboard">
            <Head title="Customer Dashboard" />

            <div className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <StatCard
                        title="Total Invoices"
                        value={stats.totalInvoices}
                        icon={DocumentTextIcon}
                        color="primary"
                    />
                    <StatCard
                        title="Total Revenue"
                        value={formatCurrency(stats.totalRevenue)}
                        icon={ArrowTrendingUpIcon}
                        color="green"
                    />
                    <StatCard
                        title="Total Expenses"
                        value={formatCurrency(stats.totalExpenses)}
                        icon={ArrowTrendingDownIcon}
                        color="red"
                    />
                    <StatCard
                        title="Net Profit/Loss"
                        value={formatCurrency(stats.totalProfit)}
                        icon={CurrencyPoundIcon}
                        color={stats.totalProfit >= 0 ? 'green' : 'red'}
                    />
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <StatCard
                        title="Total Customers"
                        value={stats.totalCustomers}
                        icon={UserGroupIcon}
                        color="blue"
                    />
                    <StatCard
                        title="Total Estimates"
                        value={stats.totalEstimates}
                        icon={ClipboardDocumentListIcon}
                        color="purple"
                    />
                    <StatCard
                        title="Pending Invoices"
                        value={stats.pendingInvoices}
                        icon={DocumentTextIcon}
                        color="yellow"
                    />
                </div>

                <div className="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div className="px-6 py-4 border-b border-gray-200">
                        <h3 className="text-lg font-semibold text-gray-900">Recent Activity</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="bg-gray-50 border-b border-gray-200">
                                    <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                    <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reference</th>
                                    <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                                    <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                                    <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                    <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200">
                                {recentActivity.length === 0 ? (
                                    <tr>
                                        <td colSpan="6" className="px-6 py-12 text-center text-gray-500">
                                            No recent activity
                                        </td>
                                    </tr>
                                ) : (
                                    recentActivity.map((activity, index) => (
                                        <tr key={index} className="hover:bg-gray-50">
                                            <td className="px-6 py-4">
                                                <span className={`inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    activity.type === 'invoice' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'
                                                }`}>
                                                    {activity.type === 'invoice' ? 'Invoice' : 'Expense'}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-900">{activity.reference}</td>
                                            <td className="px-6 py-4 text-sm text-gray-600">{activity.customer_name || '-'}</td>
                                            <td className="px-6 py-4 text-sm font-medium text-gray-900">{formatCurrency(activity.amount)}</td>
                                            <td className="px-6 py-4">{getStatusBadge(activity.status, activity.type)}</td>
                                            <td className="px-6 py-4 text-sm text-gray-600">{formatDate(activity.created_at)}</td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </CustomerLayout>
    );
}
