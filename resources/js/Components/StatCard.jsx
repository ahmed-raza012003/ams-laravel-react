export default function StatCard({ title, value, icon: Icon, trend, trendUp, color = 'primary' }) {
    const colorClasses = {
        primary: 'bg-[#2ca48b]/10 text-[#2ca48b]',
        blue: 'bg-blue-100 text-blue-600',
        green: 'bg-green-100 text-green-600',
        red: 'bg-red-100 text-red-600',
        yellow: 'bg-yellow-100 text-yellow-600',
        purple: 'bg-purple-100 text-purple-600',
    };

    return (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-sm font-medium text-gray-500">{title}</p>
                    <p className="mt-2 text-3xl font-bold text-gray-900">{value}</p>
                    {trend && (
                        <p className={`mt-2 text-sm ${trendUp ? 'text-green-600' : 'text-red-600'}`}>
                            {trendUp ? '↑' : '↓'} {trend}
                        </p>
                    )}
                </div>
                {Icon && (
                    <div className={`p-3 rounded-xl ${colorClasses[color]}`}>
                        <Icon className="w-6 h-6" />
                    </div>
                )}
            </div>
        </div>
    );
}
