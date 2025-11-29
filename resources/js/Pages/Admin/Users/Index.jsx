import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import DataTable from '@/Components/DataTable';
import Modal from '@/Components/Modal';
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon } from '@heroicons/react/24/outline';

export default function Index({ users, roles }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);

    const createForm = useForm({ name: '', email: '', password: '', roleId: '' });
    const editForm = useForm({ name: '', email: '', password: '', roleId: '' });

    const handleCreate = (e) => { e.preventDefault(); createForm.post('/admin/users', { onSuccess: () => { setShowCreateModal(false); createForm.reset(); } }); };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? '-' : date.toLocaleDateString('en-GB');
    };

    const handleEdit = (user) => {
        setSelectedUser(user);
        editForm.setData({ name: user.name || '', email: user.email || '', password: '', roleId: user.role_id || '' });
        setShowEditModal(true);
    };

    const handleUpdate = (e) => { e.preventDefault(); editForm.put(`/admin/users/${selectedUser.id}`, { onSuccess: () => { setShowEditModal(false); setSelectedUser(null); } }); };
    const handleView = (user) => { setSelectedUser(user); setShowViewModal(true); };
    const handleDelete = (user) => { setSelectedUser(user); setShowDeleteModal(true); };
    const confirmDelete = () => { router.delete(`/admin/users/${selectedUser.id}`, { onSuccess: () => { setShowDeleteModal(false); setSelectedUser(null); } }); };

    const columns = [
        { key: 'name', label: 'Name' },
        { key: 'email', label: 'Email' },
        { key: 'role_name', label: 'Role', render: (val) => <span className={`px-2.5 py-0.5 rounded-full text-xs font-medium ${val === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'}`}>{val}</span> },
        { key: 'created_at', label: 'Created', render: (val) => formatDate(val) },
    ];

    const renderActions = (user) => (
        <>
            <button onClick={() => handleView(user)} className="p-2 text-gray-600 hover:text-[#2ca48b] hover:bg-gray-100 rounded-lg transition-colors"><EyeIcon className="w-4 h-4" /></button>
            <button onClick={() => handleEdit(user)} className="p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors"><PencilIcon className="w-4 h-4" /></button>
            <button onClick={() => handleDelete(user)} className="p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors"><TrashIcon className="w-4 h-4" /></button>
        </>
    );

    return (
        <AdminLayout title="Users">
            <Head title="Users" />
            <div className="mb-6 flex justify-between items-center">
                <p className="text-gray-600">Manage system users</p>
                <button onClick={() => setShowCreateModal(true)} className="flex items-center px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] transition-colors"><PlusIcon className="w-5 h-5 mr-2" />Add User</button>
            </div>
            <DataTable columns={columns} data={users} actions={renderActions} searchPlaceholder="Search users..." emptyMessage="No users found" />

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)} title="Add User" maxWidth="md">
                <form onSubmit={handleCreate} className="space-y-4">
                    <div><label className="block text-sm font-medium text-gray-700 mb-1">Name *</label><input type="text" value={createForm.data.name} onChange={e => createForm.setData('name', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required />{createForm.errors.name && <p className="mt-1 text-sm text-red-600">{createForm.errors.name}</p>}</div>
                    <div><label className="block text-sm font-medium text-gray-700 mb-1">Email *</label><input type="email" value={createForm.data.email} onChange={e => createForm.setData('email', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required />{createForm.errors.email && <p className="mt-1 text-sm text-red-600">{createForm.errors.email}</p>}</div>
                    <div><label className="block text-sm font-medium text-gray-700 mb-1">Password *</label><input type="password" value={createForm.data.password} onChange={e => createForm.setData('password', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required />{createForm.errors.password && <p className="mt-1 text-sm text-red-600">{createForm.errors.password}</p>}</div>
                    <div><label className="block text-sm font-medium text-gray-700 mb-1">Role *</label><select value={createForm.data.roleId} onChange={e => createForm.setData('roleId', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required><option value="">Select role</option>{roles.map(r => <option key={r.id} value={r.id}>{r.name}</option>)}</select></div>
                    <div className="flex justify-end space-x-3 pt-4 border-t"><button type="button" onClick={() => setShowCreateModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button><button type="submit" disabled={createForm.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50">Create User</button></div>
                </form>
            </Modal>

            <Modal show={showEditModal} onClose={() => setShowEditModal(false)} title="Edit User" maxWidth="md">
                <form onSubmit={handleUpdate} className="space-y-4">
                    <div><label className="block text-sm font-medium text-gray-700 mb-1">Name *</label><input type="text" value={editForm.data.name} onChange={e => editForm.setData('name', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required /></div>
                    <div><label className="block text-sm font-medium text-gray-700 mb-1">Email *</label><input type="email" value={editForm.data.email} onChange={e => editForm.setData('email', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required /></div>
                    <div><label className="block text-sm font-medium text-gray-700 mb-1">Password (leave blank to keep current)</label><input type="password" value={editForm.data.password} onChange={e => editForm.setData('password', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" /></div>
                    <div><label className="block text-sm font-medium text-gray-700 mb-1">Role *</label><select value={editForm.data.roleId} onChange={e => editForm.setData('roleId', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required><option value="">Select role</option>{roles.map(r => <option key={r.id} value={r.id}>{r.name}</option>)}</select></div>
                    <div className="flex justify-end space-x-3 pt-4 border-t"><button type="button" onClick={() => setShowEditModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button><button type="submit" disabled={editForm.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50">Update User</button></div>
                </form>
            </Modal>

            <Modal show={showViewModal} onClose={() => setShowViewModal(false)} title="User Details" maxWidth="md">
                {selectedUser && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div><span className="text-sm text-gray-500">Name</span><p className="font-medium">{selectedUser.name}</p></div>
                            <div><span className="text-sm text-gray-500">Email</span><p className="font-medium">{selectedUser.email}</p></div>
                            <div><span className="text-sm text-gray-500">Role</span><p className="font-medium capitalize">{selectedUser.role_name}</p></div>
                            <div><span className="text-sm text-gray-500">Created</span><p className="font-medium">{formatDate(selectedUser.created_at)}</p></div>
                        </div>
                    </div>
                )}
            </Modal>

            <Modal show={showDeleteModal} onClose={() => setShowDeleteModal(false)} title="Delete User" maxWidth="md">
                <div className="space-y-4">
                    <p className="text-gray-600">Are you sure you want to delete <strong>{selectedUser?.name}</strong>? This action cannot be undone.</p>
                    <div className="flex justify-end space-x-3"><button onClick={() => setShowDeleteModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button><button onClick={confirmDelete} className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button></div>
                </div>
            </Modal>
        </AdminLayout>
    );
}
