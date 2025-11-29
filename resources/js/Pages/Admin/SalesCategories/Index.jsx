import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import DataTable from '@/Components/DataTable';
import Modal from '@/Components/Modal';
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon } from '@heroicons/react/24/outline';

export default function Index({ categories }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedCategory, setSelectedCategory] = useState(null);

    const createForm = useForm({ title: '', description: '' });
    const editForm = useForm({ title: '', description: '' });

    const handleCreate = (e) => {
        e.preventDefault();
        createForm.post('/admin/sales-categories', {
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

    const handleEdit = (category) => {
        setSelectedCategory(category);
        editForm.setData({
            title: category.title || '',
            description: category.description || ''
        });
        setShowEditModal(true);
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        editForm.put(`/admin/sales-categories/${selectedCategory.id}`, {
            onSuccess: () => {
                setShowEditModal(false);
                setSelectedCategory(null);
            }
        });
    };

    const handleView = (category) => {
        setSelectedCategory(category);
        setShowViewModal(true);
    };

    const handleDelete = (category) => {
        setSelectedCategory(category);
        setShowDeleteModal(true);
    };

    const confirmDelete = () => {
        router.delete(`/admin/sales-categories/${selectedCategory.id}`, {
            onSuccess: () => {
                setShowDeleteModal(false);
                setSelectedCategory(null);
            }
        });
    };

    const columns = [
        { key: 'title', label: 'Title' },
        { key: 'description', label: 'Description', render: (val) => val ? (val.length > 50 ? val.substring(0, 50) + '...' : val) : '-' },
        { key: 'created_at', label: 'Created', render: (val) => formatDate(val) },
    ];

    const renderActions = (category) => (
        <>
            <button onClick={() => handleView(category)} className="p-2 text-gray-600 hover:text-[#2ca48b] hover:bg-gray-100 rounded-lg transition-colors">
                <EyeIcon className="w-4 h-4" />
            </button>
            <button onClick={() => handleEdit(category)} className="p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors">
                <PencilIcon className="w-4 h-4" />
            </button>
            <button onClick={() => handleDelete(category)} className="p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors">
                <TrashIcon className="w-4 h-4" />
            </button>
        </>
    );

    return (
        <AdminLayout title="Sales Categories">
            <Head title="Sales Categories" />
            <div className="mb-6 flex justify-between items-center">
                <p className="text-gray-600">Manage sales categories</p>
                <button onClick={() => setShowCreateModal(true)} className="flex items-center px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] transition-colors">
                    <PlusIcon className="w-5 h-5 mr-2" />Add Category
                </button>
            </div>
            <DataTable columns={columns} data={categories} actions={renderActions} searchPlaceholder="Search categories..." emptyMessage="No categories found" />

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)} title="Add Sales Category" maxWidth="md">
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
                        <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea
                            value={createForm.data.description}
                            onChange={e => createForm.setData('description', e.target.value)}
                            rows="3"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]"
                        />
                        {createForm.errors.description && <p className="mt-1 text-sm text-red-600">{createForm.errors.description}</p>}
                    </div>
                    <div className="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onClick={() => setShowCreateModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button type="submit" disabled={createForm.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50">Create Category</button>
                    </div>
                </form>
            </Modal>

            <Modal show={showEditModal} onClose={() => setShowEditModal(false)} title="Edit Sales Category" maxWidth="md">
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
                        <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea
                            value={editForm.data.description}
                            onChange={e => editForm.setData('description', e.target.value)}
                            rows="3"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]"
                        />
                    </div>
                    <div className="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onClick={() => setShowEditModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button type="submit" disabled={editForm.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50">Update Category</button>
                    </div>
                </form>
            </Modal>

            <Modal show={showViewModal} onClose={() => setShowViewModal(false)} title="Category Details" maxWidth="md">
                {selectedCategory && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-1 gap-4">
                            <div>
                                <span className="text-sm text-gray-500">Title</span>
                                <p className="font-medium">{selectedCategory.title}</p>
                            </div>
                            <div>
                                <span className="text-sm text-gray-500">Description</span>
                                <p className="font-medium">{selectedCategory.description || '-'}</p>
                            </div>
                            <div>
                                <span className="text-sm text-gray-500">Created</span>
                                <p className="font-medium">{formatDate(selectedCategory.created_at)}</p>
                            </div>
                        </div>
                    </div>
                )}
            </Modal>

            <Modal show={showDeleteModal} onClose={() => setShowDeleteModal(false)} title="Delete Category" maxWidth="md">
                <div className="space-y-4">
                    <p className="text-gray-600">Are you sure you want to delete <strong>{selectedCategory?.title}</strong>? This action cannot be undone.</p>
                    <div className="flex justify-end space-x-3">
                        <button onClick={() => setShowDeleteModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button onClick={confirmDelete} className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                    </div>
                </div>
            </Modal>
        </AdminLayout>
    );
}

