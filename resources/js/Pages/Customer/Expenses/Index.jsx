import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import CustomerLayout from '@/Layouts/CustomerLayout';
import DataTable from '@/Components/DataTable';
import Modal from '@/Components/Modal';
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon } from '@heroicons/react/24/outline';

export default function Index({ expenses, customers, categories, currency }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedExpense, setSelectedExpense] = useState(null);

    const formatCurrency = (amount) => `${currency}${Number(amount).toFixed(2)}`;
    const formatDate = (date) => new Date(date).toLocaleDateString('en-GB');

    const createForm = useForm({ category: 'OTHER', description: '', amount: '', taxAmount: '0', date: new Date().toISOString().split('T')[0], customerId: '', notes: '' });
    const editForm = useForm({ category: '', description: '', amount: '', taxAmount: '', date: '', customerId: '', notes: '' });

    const handleCreate = (e) => {
        e.preventDefault();
        createForm.post('/customer/expenses', { onSuccess: () => { setShowCreateModal(false); createForm.reset(); } });
    };

    const handleEdit = (expense) => {
        setSelectedExpense(expense);
        editForm.setData({
            category: expense.category || 'OTHER',
            description: expense.description || '',
            amount: expense.amount || '',
            taxAmount: expense.taxAmount || '0',
            date: expense.date ? expense.date.split('T')[0] : '',
            customerId: expense.customerId || '',
            notes: expense.notes || '',
        });
        setShowEditModal(true);
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        editForm.put(`/customer/expenses/${selectedExpense.id}`, { onSuccess: () => { setShowEditModal(false); setSelectedExpense(null); } });
    };

    const handleView = (expense) => { setSelectedExpense(expense); setShowViewModal(true); };
    const handleDelete = (expense) => { setSelectedExpense(expense); setShowDeleteModal(true); };
    const confirmDelete = () => { router.delete(`/customer/expenses/${selectedExpense.id}`, { onSuccess: () => { setShowDeleteModal(false); setSelectedExpense(null); } }); };

    const columns = [
        { key: 'date', label: 'Date', render: (val) => formatDate(val) },
        { key: 'category', label: 'Category', render: (val) => categories[val] || val },
        { key: 'description', label: 'Description', render: (val) => val.length > 40 ? val.substring(0, 40) + '...' : val },
        { key: 'customer_name', label: 'Customer', render: (val) => val || '-' },
        { key: 'amount', label: 'Amount', render: (val) => formatCurrency(val) },
    ];

    const renderActions = (expense) => (
        <>
            <button onClick={() => handleView(expense)} className="p-2 text-gray-600 hover:text-[#2ca48b] hover:bg-gray-100 rounded-lg transition-colors"><EyeIcon className="w-4 h-4" /></button>
            <button onClick={() => handleEdit(expense)} className="p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors"><PencilIcon className="w-4 h-4" /></button>
            <button onClick={() => handleDelete(expense)} className="p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors"><TrashIcon className="w-4 h-4" /></button>
        </>
    );

    const FormFields = ({ form, onSubmit, submitText }) => (
        <form onSubmit={onSubmit} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select value={form.data.category} onChange={e => form.setData('category', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" required>
                        {Object.entries(categories).map(([key, label]) => <option key={key} value={key}>{label}</option>)}
                    </select>
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" value={form.data.date} onChange={e => form.setData('date', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" required />
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                    <input type="number" step="0.01" min="0" value={form.data.amount} onChange={e => form.setData('amount', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" required />
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Tax Amount</label>
                    <input type="number" step="0.01" min="0" value={form.data.taxAmount} onChange={e => form.setData('taxAmount', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
                </div>
                <div className="md:col-span-2">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Customer (optional)</label>
                    <select value={form.data.customerId} onChange={e => form.setData('customerId', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]">
                        <option value="">None</option>
                        {customers.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                    </select>
                </div>
            </div>
            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <input type="text" value={form.data.description} onChange={e => form.setData('description', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" required />
                {form.errors.description && <p className="mt-1 text-sm text-red-600">{form.errors.description}</p>}
            </div>
            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea value={form.data.notes} onChange={e => form.setData('notes', e.target.value)} rows="3" className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
            </div>
            <div className="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onClick={() => { setShowCreateModal(false); setShowEditModal(false); }} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" disabled={form.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50 transition-colors">{submitText}</button>
            </div>
        </form>
    );

    return (
        <CustomerLayout title="Expenses">
            <Head title="Expenses" />
            <div className="mb-6 flex justify-between items-center">
                <p className="text-gray-600">Track your business expenses</p>
                <button onClick={() => setShowCreateModal(true)} className="flex items-center px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] transition-colors">
                    <PlusIcon className="w-5 h-5 mr-2" />Add Expense
                </button>
            </div>
            <DataTable columns={columns} data={expenses} actions={renderActions} searchPlaceholder="Search expenses..." emptyMessage="No expenses found" />

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)} title="Add Expense" maxWidth="lg">
                <FormFields form={createForm} onSubmit={handleCreate} submitText="Create Expense" />
            </Modal>

            <Modal show={showEditModal} onClose={() => setShowEditModal(false)} title="Edit Expense" maxWidth="lg">
                <FormFields form={editForm} onSubmit={handleUpdate} submitText="Update Expense" />
            </Modal>

            <Modal show={showViewModal} onClose={() => setShowViewModal(false)} title="Expense Details" maxWidth="md">
                {selectedExpense && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div><span className="text-sm text-gray-500">Category</span><p className="font-medium">{categories[selectedExpense.category]}</p></div>
                            <div><span className="text-sm text-gray-500">Date</span><p className="font-medium">{formatDate(selectedExpense.date)}</p></div>
                            <div><span className="text-sm text-gray-500">Amount</span><p className="font-medium">{formatCurrency(selectedExpense.amount)}</p></div>
                            <div><span className="text-sm text-gray-500">Tax Amount</span><p className="font-medium">{formatCurrency(selectedExpense.taxAmount)}</p></div>
                            <div><span className="text-sm text-gray-500">Customer</span><p className="font-medium">{selectedExpense.customer_name || '-'}</p></div>
                        </div>
                        <div><span className="text-sm text-gray-500">Description</span><p className="font-medium">{selectedExpense.description}</p></div>
                        <div><span className="text-sm text-gray-500">Notes</span><p className="font-medium">{selectedExpense.notes || '-'}</p></div>
                    </div>
                )}
            </Modal>

            <Modal show={showDeleteModal} onClose={() => setShowDeleteModal(false)} title="Delete Expense" maxWidth="md">
                <div className="space-y-4">
                    <p className="text-gray-600">Are you sure you want to delete this expense? This action cannot be undone.</p>
                    <div className="flex justify-end space-x-3">
                        <button onClick={() => setShowDeleteModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                        <button onClick={confirmDelete} className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Delete</button>
                    </div>
                </div>
            </Modal>
        </CustomerLayout>
    );
}
