import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import DataTable from '@/Components/DataTable';
import Modal from '@/Components/Modal';
import ExportButton from '@/Components/ExportButton';
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon } from '@heroicons/react/24/outline';

export default function Index({ customers }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedCustomer, setSelectedCustomer] = useState(null);

    const createForm = useForm({
        name: '',
        email: '',
        phone: '',
        address: '',
        city: '',
        postcode: '',
        country: 'United Kingdom',
        notes: '',
    });

    const editForm = useForm({
        name: '',
        email: '',
        phone: '',
        address: '',
        city: '',
        postcode: '',
        country: '',
        notes: '',
    });

    const handleCreate = (e) => {
        e.preventDefault();
        createForm.post('/admin/customers', {
            onSuccess: () => {
                setShowCreateModal(false);
                createForm.reset();
            },
        });
    };

    const handleEdit = (customer) => {
        setSelectedCustomer(customer);
        editForm.setData({
            name: customer.name || '',
            email: customer.email || '',
            phone: customer.phone || '',
            address: customer.address || '',
            city: customer.city || '',
            postcode: customer.postcode || '',
            country: customer.country || 'United Kingdom',
            notes: customer.notes || '',
        });
        setShowEditModal(true);
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        editForm.put(`/admin/customers/${selectedCustomer.id}`, {
            onSuccess: () => {
                setShowEditModal(false);
                setSelectedCustomer(null);
            },
        });
    };

    const handleView = (customer) => {
        setSelectedCustomer(customer);
        setShowViewModal(true);
    };

    const handleDelete = (customer) => {
        setSelectedCustomer(customer);
        setShowDeleteModal(true);
    };

    const confirmDelete = () => {
        router.delete(`/admin/customers/${selectedCustomer.id}`, {
            onSuccess: () => {
                setShowDeleteModal(false);
                setSelectedCustomer(null);
            },
        });
    };

    const columns = [
        { key: 'name', label: 'Name' },
        { key: 'email', label: 'Email' },
        { key: 'phone', label: 'Phone' },
        { key: 'city', label: 'City' },
        { key: 'country', label: 'Country' },
    ];

    const renderActions = (customer) => (
        <>
            <button onClick={() => handleView(customer)} className="p-2 text-gray-600 hover:text-[#2ca48b] hover:bg-gray-100 rounded-lg transition-colors">
                <EyeIcon className="w-4 h-4" />
            </button>
            <button onClick={() => handleEdit(customer)} className="p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors">
                <PencilIcon className="w-4 h-4" />
            </button>
            <button onClick={() => handleDelete(customer)} className="p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors">
                <TrashIcon className="w-4 h-4" />
            </button>
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
                    <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" value={form.data.email} onChange={e => form.setData('email', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
                    {form.errors.email && <p className="mt-1 text-sm text-red-600">{form.errors.email}</p>}
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" value={form.data.phone} onChange={e => form.setData('phone', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" value={form.data.city} onChange={e => form.setData('city', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                    <input type="text" value={form.data.postcode} onChange={e => form.setData('postcode', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" value={form.data.country} onChange={e => form.setData('country', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
                </div>
            </div>
            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" value={form.data.address} onChange={e => form.setData('address', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b] focus:border-[#2ca48b]" />
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
        <AdminLayout title="Customers">
            <Head title="Customers" />
            <div className="mb-6 flex justify-between items-center">
                <p className="text-gray-600">Manage your customers</p>
                <div className="flex items-center space-x-3">
                    <ExportButton 
                        pdfUrl="/admin/customers/export/pdf"
                        excelUrl="/admin/customers/export/excel"
                    />
                    <button onClick={() => setShowCreateModal(true)} className="flex items-center px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] transition-colors">
                        <PlusIcon className="w-5 h-5 mr-2" />
                        Add Customer
                    </button>
                </div>
            </div>
            <DataTable columns={columns} data={customers} actions={renderActions} searchPlaceholder="Search customers..." emptyMessage="No customers found" />

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)} title="Add Customer" maxWidth="2xl">
                <FormFields form={createForm} onSubmit={handleCreate} submitText="Create Customer" />
            </Modal>

            <Modal show={showEditModal} onClose={() => setShowEditModal(false)} title="Edit Customer" maxWidth="2xl">
                <FormFields form={editForm} onSubmit={handleUpdate} submitText="Update Customer" />
            </Modal>

            <Modal show={showViewModal} onClose={() => setShowViewModal(false)} title="Customer Details" maxWidth="lg">
                {selectedCustomer && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div><span className="text-sm text-gray-500">Name</span><p className="font-medium">{selectedCustomer.name}</p></div>
                            <div><span className="text-sm text-gray-500">Email</span><p className="font-medium">{selectedCustomer.email || '-'}</p></div>
                            <div><span className="text-sm text-gray-500">Phone</span><p className="font-medium">{selectedCustomer.phone || '-'}</p></div>
                            <div><span className="text-sm text-gray-500">City</span><p className="font-medium">{selectedCustomer.city || '-'}</p></div>
                            <div><span className="text-sm text-gray-500">Postcode</span><p className="font-medium">{selectedCustomer.postcode || '-'}</p></div>
                            <div><span className="text-sm text-gray-500">Country</span><p className="font-medium">{selectedCustomer.country || '-'}</p></div>
                        </div>
                        <div><span className="text-sm text-gray-500">Address</span><p className="font-medium">{selectedCustomer.address || '-'}</p></div>
                        <div><span className="text-sm text-gray-500">Notes</span><p className="font-medium">{selectedCustomer.notes || '-'}</p></div>
                    </div>
                )}
            </Modal>

            <Modal show={showDeleteModal} onClose={() => setShowDeleteModal(false)} title="Delete Customer" maxWidth="md">
                <div className="space-y-4">
                    <p className="text-gray-600">Are you sure you want to delete <strong>{selectedCustomer?.name}</strong>? This action cannot be undone.</p>
                    <div className="flex justify-end space-x-3">
                        <button onClick={() => setShowDeleteModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                        <button onClick={confirmDelete} className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Delete</button>
                    </div>
                </div>
            </Modal>
        </AdminLayout>
    );
}
