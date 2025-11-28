import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import DataTable from '@/Components/DataTable';
import Modal from '@/Components/Modal';
import ExportButton from '@/Components/ExportButton';
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon } from '@heroicons/react/24/outline';

export default function Index({ items, currency }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedItem, setSelectedItem] = useState(null);

    const formatCurrency = (amount) => {
        if (amount == null || isNaN(amount)) return `${currency}0.00`;
        const num = Number(amount);
        return isNaN(num) ? `${currency}0.00` : `${currency}${num.toFixed(2)}`;
    };

    const createForm = useForm({ name: '', description: '', unitPrice: '', unit: 'unit', taxRate: '0' });
    const editForm = useForm({ name: '', description: '', unitPrice: '', unit: '', taxRate: '' });

    const handleCreate = (e) => {
        e.preventDefault();
        createForm.post('/admin/items', { onSuccess: () => { setShowCreateModal(false); createForm.reset(); } });
    };

    const handleEdit = (item) => {
        setSelectedItem(item);
        editForm.setData({ name: item.name || '', description: item.description || '', unitPrice: item.unit_price || '', unit: item.unit || 'unit', taxRate: item.tax_rate || '0' });
        setShowEditModal(true);
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        editForm.put(`/admin/items/${selectedItem.id}`, { onSuccess: () => { setShowEditModal(false); setSelectedItem(null); } });
    };

    const handleView = (item) => { setSelectedItem(item); setShowViewModal(true); };
    const handleDelete = (item) => { setSelectedItem(item); setShowDeleteModal(true); };
    const confirmDelete = () => { router.delete(`/admin/items/${selectedItem.id}`, { onSuccess: () => { setShowDeleteModal(false); setSelectedItem(null); } }); };

    const columns = [
        { key: 'name', label: 'Name' },
        { key: 'description', label: 'Description', render: (val) => val ? (val.length > 50 ? val.substring(0, 50) + '...' : val) : '-' },
        { key: 'unit_price', label: 'Unit Price', render: (val) => formatCurrency(val) },
        { key: 'unit', label: 'Unit' },
        { key: 'tax_rate', label: 'Tax Rate', render: (val) => val != null ? `${val}%` : '0%' },
    ];

    const renderActions = (item) => (
        <>
            <button onClick={() => handleView(item)} className="p-2 text-gray-600 hover:text-[#2ca48b] hover:bg-gray-100 rounded-lg transition-colors"><EyeIcon className="w-4 h-4" /></button>
            <button onClick={() => handleEdit(item)} className="p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors"><PencilIcon className="w-4 h-4" /></button>
            <button onClick={() => handleDelete(item)} className="p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors"><TrashIcon className="w-4 h-4" /></button>
        </>
    );

    const FormFields = ({ form, onSubmit, submitText }) => (
        <form onSubmit={onSubmit} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" value={form.data.name} onChange={e => form.setData('name', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" required />
                    {form.errors.name && <p className="mt-1 text-sm text-red-600">{form.errors.name}</p>}
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                    <input type="number" step="0.01" min="0" value={form.data.unitPrice} onChange={e => form.setData('unitPrice', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" required />
                    {form.errors.unitPrice && <p className="mt-1 text-sm text-red-600">{form.errors.unitPrice}</p>}
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <input type="text" value={form.data.unit} onChange={e => form.setData('unit', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Tax Rate (%)</label>
                    <input type="number" step="0.01" min="0" max="100" value={form.data.taxRate} onChange={e => form.setData('taxRate', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
                </div>
            </div>
            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea value={form.data.description} onChange={e => form.setData('description', e.target.value)} rows="3" className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
            </div>
            <div className="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onClick={() => { setShowCreateModal(false); setShowEditModal(false); }} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" disabled={form.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50 transition-colors">{submitText}</button>
            </div>
        </form>
    );

    return (
        <AdminLayout title="Items">
            <Head title="Items" />
            <div className="mb-6 flex justify-between items-center">
                <p className="text-gray-600">Manage your products and services</p>
                <div className="flex items-center space-x-3">
                    <ExportButton 
                        pdfUrl="/admin/items/export/pdf"
                        excelUrl="/admin/items/export/excel"
                    />
                    <button onClick={() => setShowCreateModal(true)} className="flex items-center px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] transition-colors">
                        <PlusIcon className="w-5 h-5 mr-2" />Add Item
                    </button>
                </div>
            </div>
            <DataTable columns={columns} data={items} actions={renderActions} searchPlaceholder="Search items..." emptyMessage="No items found" />

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)} title="Add Item" maxWidth="lg">
                <FormFields form={createForm} onSubmit={handleCreate} submitText="Create Item" />
            </Modal>

            <Modal show={showEditModal} onClose={() => setShowEditModal(false)} title="Edit Item" maxWidth="lg">
                <FormFields form={editForm} onSubmit={handleUpdate} submitText="Update Item" />
            </Modal>

            <Modal show={showViewModal} onClose={() => setShowViewModal(false)} title="Item Details" maxWidth="md">
                {selectedItem && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div><span className="text-sm text-gray-500">Name</span><p className="font-medium">{selectedItem.name}</p></div>
                            <div><span className="text-sm text-gray-500">Unit Price</span><p className="font-medium">{formatCurrency(selectedItem.unit_price)}</p></div>
                            <div><span className="text-sm text-gray-500">Unit</span><p className="font-medium">{selectedItem.unit || '-'}</p></div>
                            <div><span className="text-sm text-gray-500">Tax Rate</span><p className="font-medium">{selectedItem.tax_rate != null ? `${selectedItem.tax_rate}%` : '0%'}</p></div>
                        </div>
                        <div><span className="text-sm text-gray-500">Description</span><p className="font-medium">{selectedItem.description || '-'}</p></div>
                    </div>
                )}
            </Modal>

            <Modal show={showDeleteModal} onClose={() => setShowDeleteModal(false)} title="Delete Item" maxWidth="md">
                <div className="space-y-4">
                    <p className="text-gray-600">Are you sure you want to delete <strong>{selectedItem?.name}</strong>? This action cannot be undone.</p>
                    <div className="flex justify-end space-x-3">
                        <button onClick={() => setShowDeleteModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                        <button onClick={confirmDelete} className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Delete</button>
                    </div>
                </div>
            </Modal>
        </AdminLayout>
    );
}
