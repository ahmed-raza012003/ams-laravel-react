import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import CustomerLayout from '@/Layouts/CustomerLayout';
import DataTable from '@/Components/DataTable';
import Modal from '@/Components/Modal';
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon } from '@heroicons/react/24/outline';

export default function Index({ taxTypes }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedTaxType, setSelectedTaxType] = useState(null);

    const createForm = useForm({ title: '', rate: '' });
    const editForm = useForm({ title: '', rate: '' });

    const handleCreate = (e) => {
        e.preventDefault();
        createForm.post('/customer/tax-types', {
            onSuccess: () => {
                setShowCreateModal(false);
                createForm.reset();
            }
        });
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? '-' : date.toLocaleDateString('en-GB');
    };

    const handleEdit = (taxType) => {
        setSelectedTaxType(taxType);
        editForm.setData({
            title: taxType.title || '',
            rate: taxType.rate || ''
        });
        setShowEditModal(true);
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        editForm.put(`/customer/tax-types/${selectedTaxType.id}`, {
            onSuccess: () => {
                setShowEditModal(false);
                setSelectedTaxType(null);
            }
        });
    };

    const handleView = (taxType) => {
        setSelectedTaxType(taxType);
        setShowViewModal(true);
    };

    const handleDelete = (taxType) => {
        setSelectedTaxType(taxType);
        setShowDeleteModal(true);
    };

    const confirmDelete = () => {
        router.delete(`/customer/tax-types/${selectedTaxType.id}`, {
            onSuccess: () => {
                setShowDeleteModal(false);
                setSelectedTaxType(null);
            }
        });
    };

    const columns = [
        { key: 'title', label: 'Title' },
        { key: 'rate', label: 'Rate (%)', render: (val) => `${val}%` },
        { key: 'created_at', label: 'Created', render: (val) => formatDate(val) },
    ];

    const renderActions = (taxType) => (
        <>
            <button onClick={() => handleView(taxType)} className="p-2 text-gray-600 hover:text-[#2ca48b] hover:bg-gray-100 rounded-lg transition-colors">
                <EyeIcon className="w-4 h-4" />
            </button>
            <button onClick={() => handleEdit(taxType)} className="p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors">
                <PencilIcon className="w-4 h-4" />
            </button>
            <button onClick={() => handleDelete(taxType)} className="p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors">
                <TrashIcon className="w-4 h-4" />
            </button>
        </>
    );

    return (
        <CustomerLayout title="Tax Types">
            <Head title="Tax Types" />
            <div className="mb-6 flex justify-between items-center">
                <p className="text-gray-600">Manage tax types</p>
                <button onClick={() => setShowCreateModal(true)} className="flex items-center px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] transition-colors">
                    <PlusIcon className="w-5 h-5 mr-2" />Add Tax Type
                </button>
            </div>
            <DataTable columns={columns} data={taxTypes} actions={renderActions} searchPlaceholder="Search tax types..." emptyMessage="No tax types found" />

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)} title="Add Tax Type" maxWidth="md">
                <form onSubmit={handleCreate} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input
                            type="text"
                            value={createForm.data.title}
                            onChange={e => createForm.setData('title', e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]"
                            required
                        />
                        {createForm.errors.title && <p className="mt-1 text-sm text-red-600">{createForm.errors.title}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Rate (%) *</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            value={createForm.data.rate}
                            onChange={e => createForm.setData('rate', e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]"
                            required
                        />
                        {createForm.errors.rate && <p className="mt-1 text-sm text-red-600">{createForm.errors.rate}</p>}
                    </div>
                    <div className="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onClick={() => setShowCreateModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button type="submit" disabled={createForm.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50">Create Tax Type</button>
                    </div>
                </form>
            </Modal>

            <Modal show={showEditModal} onClose={() => setShowEditModal(false)} title="Edit Tax Type" maxWidth="md">
                <form onSubmit={handleUpdate} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input
                            type="text"
                            value={editForm.data.title}
                            onChange={e => editForm.setData('title', e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]"
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Rate (%) *</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            value={editForm.data.rate}
                            onChange={e => editForm.setData('rate', e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]"
                            required
                        />
                    </div>
                    <div className="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onClick={() => setShowEditModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button type="submit" disabled={editForm.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50">Update Tax Type</button>
                    </div>
                </form>
            </Modal>

            <Modal show={showViewModal} onClose={() => setShowViewModal(false)} title="Tax Type Details" maxWidth="md">
                {selectedTaxType && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-1 gap-4">
                            <div>
                                <span className="text-sm text-gray-500">Title</span>
                                <p className="font-medium">{selectedTaxType.title}</p>
                            </div>
                            <div>
                                <span className="text-sm text-gray-500">Rate</span>
                                <p className="font-medium">{selectedTaxType.rate}%</p>
                            </div>
                            <div>
                                <span className="text-sm text-gray-500">Created</span>
                                <p className="font-medium">{formatDate(selectedTaxType.created_at)}</p>
                            </div>
                        </div>
                    </div>
                )}
            </Modal>

            <Modal show={showDeleteModal} onClose={() => setShowDeleteModal(false)} title="Delete Tax Type" maxWidth="md">
                <div className="space-y-4">
                    <p className="text-gray-600">Are you sure you want to delete <strong>{selectedTaxType?.title}</strong>? This action cannot be undone.</p>
                    <div className="flex justify-end space-x-3">
                        <button onClick={() => setShowDeleteModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button onClick={confirmDelete} className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                    </div>
                </div>
            </Modal>
        </CustomerLayout>
    );
}

