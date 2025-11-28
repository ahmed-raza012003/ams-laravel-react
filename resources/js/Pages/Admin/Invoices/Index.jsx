import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import DataTable from '@/Components/DataTable';
import Modal from '@/Components/Modal';
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon } from '@heroicons/react/24/outline';

export default function Index({ invoices, customers, items, currency }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedInvoice, setSelectedInvoice] = useState(null);

    const formatCurrency = (amount) => `${currency}${Number(amount).toFixed(2)}`;
    const formatDate = (date) => new Date(date).toLocaleDateString('en-GB');

    const emptyItem = { description: '', quantity: 1, unitPrice: '', taxRate: 0, itemId: '' };
    const createForm = useForm({ customerId: '', dueDate: '', notes: '', items: [{ ...emptyItem }] });
    const editForm = useForm({ customerId: '', dueDate: '', status: '', notes: '', items: [] });

    const addItem = (form) => form.setData('items', [...form.data.items, { ...emptyItem }]);
    const removeItem = (form, index) => form.setData('items', form.data.items.filter((_, i) => i !== index));
    const updateItem = (form, index, field, value) => {
        const newItems = [...form.data.items];
        newItems[index][field] = value;
        if (field === 'itemId' && value) {
            const item = items.find(i => i.id == value);
            if (item) {
                newItems[index].description = item.name;
                newItems[index].unitPrice = item.unitPrice;
                newItems[index].taxRate = item.taxRate || 0;
            }
        }
        form.setData('items', newItems);
    };

    const handleCreate = (e) => {
        e.preventDefault();
        createForm.post('/admin/invoices', { onSuccess: () => { setShowCreateModal(false); createForm.reset(); createForm.setData('items', [{ ...emptyItem }]); } });
    };

    const handleEdit = async (invoice) => {
        const response = await fetch(`/admin/invoices/${invoice.id}`);
        const data = await response.json();
        setSelectedInvoice(data);
        editForm.setData({
            customerId: data.customerId || '',
            dueDate: data.dueDate ? data.dueDate.split('T')[0] : '',
            status: data.status || 'DRAFT',
            notes: data.notes || '',
            items: data.items?.map(i => ({ description: i.description, quantity: i.quantity, unitPrice: i.unitPrice, taxRate: i.taxRate || 0, itemId: i.itemId || '' })) || [{ ...emptyItem }],
        });
        setShowEditModal(true);
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        editForm.put(`/admin/invoices/${selectedInvoice.id}`, { onSuccess: () => { setShowEditModal(false); setSelectedInvoice(null); } });
    };

    const handleView = async (invoice) => {
        const response = await fetch(`/admin/invoices/${invoice.id}`);
        const data = await response.json();
        setSelectedInvoice(data);
        setShowViewModal(true);
    };

    const handleDelete = (invoice) => { setSelectedInvoice(invoice); setShowDeleteModal(true); };
    const confirmDelete = () => { router.delete(`/admin/invoices/${selectedInvoice.id}`, { onSuccess: () => { setShowDeleteModal(false); setSelectedInvoice(null); } }); };

    const statusColors = { DRAFT: 'bg-gray-100 text-gray-700', SENT: 'bg-blue-100 text-blue-700', PAID: 'bg-green-100 text-green-700', OVERDUE: 'bg-red-100 text-red-700', CANCELLED: 'bg-gray-100 text-gray-500' };

    const columns = [
        { key: 'invoiceNumber', label: 'Invoice #' },
        { key: 'customer_name', label: 'Customer' },
        { key: 'issueDate', label: 'Issue Date', render: (val) => formatDate(val) },
        { key: 'dueDate', label: 'Due Date', render: (val) => formatDate(val) },
        { key: 'total', label: 'Total', render: (val) => formatCurrency(val) },
        { key: 'status', label: 'Status', render: (val) => <span className={`px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[val]}`}>{val}</span> },
    ];

    const renderActions = (invoice) => (
        <>
            <button onClick={() => handleView(invoice)} className="p-2 text-gray-600 hover:text-[#2ca48b] hover:bg-gray-100 rounded-lg transition-colors"><EyeIcon className="w-4 h-4" /></button>
            <button onClick={() => handleEdit(invoice)} className="p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors"><PencilIcon className="w-4 h-4" /></button>
            <button onClick={() => handleDelete(invoice)} className="p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors"><TrashIcon className="w-4 h-4" /></button>
        </>
    );

    const ItemsForm = ({ form }) => (
        <div className="space-y-3">
            <div className="flex justify-between items-center"><label className="text-sm font-medium text-gray-700">Line Items *</label><button type="button" onClick={() => addItem(form)} className="text-sm text-[#2ca48b] hover:underline">+ Add Item</button></div>
            {form.data.items.map((item, index) => (
                <div key={index} className="grid grid-cols-12 gap-2 items-end p-3 bg-gray-50 rounded-lg">
                    <div className="col-span-3">
                        <label className="text-xs text-gray-500">Product</label>
                        <select value={item.itemId} onChange={e => updateItem(form, index, 'itemId', e.target.value)} className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-[#2ca48b]">
                            <option value="">Custom</option>
                            {items.map(i => <option key={i.id} value={i.id}>{i.name}</option>)}
                        </select>
                    </div>
                    <div className="col-span-3">
                        <label className="text-xs text-gray-500">Description</label>
                        <input type="text" value={item.description} onChange={e => updateItem(form, index, 'description', e.target.value)} className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-[#2ca48b]" required />
                    </div>
                    <div className="col-span-2">
                        <label className="text-xs text-gray-500">Qty</label>
                        <input type="number" min="0.01" step="0.01" value={item.quantity} onChange={e => updateItem(form, index, 'quantity', e.target.value)} className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-[#2ca48b]" required />
                    </div>
                    <div className="col-span-2">
                        <label className="text-xs text-gray-500">Price</label>
                        <input type="number" min="0" step="0.01" value={item.unitPrice} onChange={e => updateItem(form, index, 'unitPrice', e.target.value)} className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-[#2ca48b]" required />
                    </div>
                    <div className="col-span-1">
                        <label className="text-xs text-gray-500">Tax%</label>
                        <input type="number" min="0" max="100" step="0.01" value={item.taxRate} onChange={e => updateItem(form, index, 'taxRate', e.target.value)} className="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-[#2ca48b]" />
                    </div>
                    <div className="col-span-1">
                        {form.data.items.length > 1 && <button type="button" onClick={() => removeItem(form, index)} className="p-1.5 text-red-500 hover:bg-red-50 rounded"><TrashIcon className="w-4 h-4" /></button>}
                    </div>
                </div>
            ))}
        </div>
    );

    return (
        <AdminLayout title="Invoices">
            <Head title="Invoices" />
            <div className="mb-6 flex justify-between items-center">
                <p className="text-gray-600">Manage your invoices</p>
                <button onClick={() => setShowCreateModal(true)} className="flex items-center px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] transition-colors">
                    <PlusIcon className="w-5 h-5 mr-2" />Create Invoice
                </button>
            </div>
            <DataTable columns={columns} data={invoices} actions={renderActions} searchPlaceholder="Search invoices..." emptyMessage="No invoices found" />

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)} title="Create Invoice" maxWidth="3xl">
                <form onSubmit={handleCreate} className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                            <select value={createForm.data.customerId} onChange={e => createForm.setData('customerId', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required>
                                <option value="">Select customer</option>
                                {customers.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                            <input type="date" value={createForm.data.dueDate} onChange={e => createForm.setData('dueDate', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required />
                        </div>
                    </div>
                    <ItemsForm form={createForm} />
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea value={createForm.data.notes} onChange={e => createForm.setData('notes', e.target.value)} rows="2" className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" />
                    </div>
                    <div className="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onClick={() => setShowCreateModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button type="submit" disabled={createForm.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50">Create Invoice</button>
                    </div>
                </form>
            </Modal>

            <Modal show={showEditModal} onClose={() => setShowEditModal(false)} title="Edit Invoice" maxWidth="3xl">
                <form onSubmit={handleUpdate} className="space-y-4">
                    <div className="grid grid-cols-3 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                            <select value={editForm.data.customerId} onChange={e => editForm.setData('customerId', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required>
                                <option value="">Select customer</option>
                                {customers.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                            <input type="date" value={editForm.data.dueDate} onChange={e => editForm.setData('dueDate', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select value={editForm.data.status} onChange={e => editForm.setData('status', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required>
                                <option value="DRAFT">Draft</option>
                                <option value="SENT">Sent</option>
                                <option value="PAID">Paid</option>
                                <option value="OVERDUE">Overdue</option>
                                <option value="CANCELLED">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <ItemsForm form={editForm} />
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea value={editForm.data.notes} onChange={e => editForm.setData('notes', e.target.value)} rows="2" className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" />
                    </div>
                    <div className="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onClick={() => setShowEditModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button type="submit" disabled={editForm.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50">Update Invoice</button>
                    </div>
                </form>
            </Modal>

            <Modal show={showViewModal} onClose={() => setShowViewModal(false)} title={`Invoice ${selectedInvoice?.invoiceNumber}`} maxWidth="2xl">
                {selectedInvoice && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-3 gap-4">
                            <div><span className="text-sm text-gray-500">Customer</span><p className="font-medium">{selectedInvoice.customer_name}</p></div>
                            <div><span className="text-sm text-gray-500">Issue Date</span><p className="font-medium">{formatDate(selectedInvoice.issueDate)}</p></div>
                            <div><span className="text-sm text-gray-500">Due Date</span><p className="font-medium">{formatDate(selectedInvoice.dueDate)}</p></div>
                        </div>
                        <div><span className="text-sm text-gray-500">Status</span><span className={`ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[selectedInvoice.status]}`}>{selectedInvoice.status}</span></div>
                        <div className="border rounded-lg overflow-hidden">
                            <table className="w-full text-sm">
                                <thead className="bg-gray-50"><tr><th className="px-4 py-2 text-left">Description</th><th className="px-4 py-2 text-right">Qty</th><th className="px-4 py-2 text-right">Price</th><th className="px-4 py-2 text-right">Tax</th><th className="px-4 py-2 text-right">Total</th></tr></thead>
                                <tbody>{selectedInvoice.items?.map((item, i) => <tr key={i} className="border-t"><td className="px-4 py-2">{item.description}</td><td className="px-4 py-2 text-right">{item.quantity}</td><td className="px-4 py-2 text-right">{formatCurrency(item.unitPrice)}</td><td className="px-4 py-2 text-right">{item.taxRate}%</td><td className="px-4 py-2 text-right font-medium">{formatCurrency(item.total)}</td></tr>)}</tbody>
                            </table>
                        </div>
                        <div className="flex justify-end">
                            <div className="w-48 space-y-1 text-sm">
                                <div className="flex justify-between"><span className="text-gray-500">Subtotal:</span><span>{formatCurrency(selectedInvoice.subtotal)}</span></div>
                                <div className="flex justify-between"><span className="text-gray-500">Tax:</span><span>{formatCurrency(selectedInvoice.taxAmount)}</span></div>
                                <div className="flex justify-between font-bold text-lg border-t pt-1"><span>Total:</span><span>{formatCurrency(selectedInvoice.total)}</span></div>
                            </div>
                        </div>
                    </div>
                )}
            </Modal>

            <Modal show={showDeleteModal} onClose={() => setShowDeleteModal(false)} title="Delete Invoice" maxWidth="md">
                <div className="space-y-4">
                    <p className="text-gray-600">Are you sure you want to delete invoice <strong>{selectedInvoice?.invoiceNumber}</strong>? This action cannot be undone.</p>
                    <div className="flex justify-end space-x-3">
                        <button onClick={() => setShowDeleteModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button onClick={confirmDelete} className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                    </div>
                </div>
            </Modal>
        </AdminLayout>
    );
}
