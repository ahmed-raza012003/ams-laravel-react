import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import DataTable from '@/Components/DataTable';
import Modal from '@/Components/Modal';
import ExportButton from '@/Components/ExportButton';
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon, CubeIcon } from '@heroicons/react/24/outline';

export default function Index({ items, categories, taxTypes, currency }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [showStockModal, setShowStockModal] = useState(false);
    const [selectedItem, setSelectedItem] = useState(null);

    const formatCurrency = (amount) => {
        if (amount == null || isNaN(amount)) return `${currency}0.00`;
        const num = Number(amount);
        return isNaN(num) ? `${currency}0.00` : `${currency}${num.toFixed(2)}`;
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? '-' : date.toLocaleDateString('en-GB');
    };

    const createForm = useForm({
        name: '',
        itemCode: '',
        description: '',
        stockQuantity: '0',
        unit: 'inches',
        purchaseDate: '',
        purchasePrice: '',
        unitPrice: '',
        salesPrice: '',
        manufacturer: '',
        warrantyInfo: '',
        notes: '',
        itemCategoryId: '',
        taxTypes: []
    });

    const editForm = useForm({
        name: '',
        itemCode: '',
        description: '',
        stockQuantity: '0',
        unit: 'inches',
        purchaseDate: '',
        purchasePrice: '',
        unitPrice: '',
        salesPrice: '',
        manufacturer: '',
        warrantyInfo: '',
        notes: '',
        itemCategoryId: '',
        taxTypes: []
    });

    const stockForm = useForm({ stockQuantity: '' });

    const handleCreate = (e) => {
        e.preventDefault();
        createForm.post('/admin/items', {
            onSuccess: () => {
                setShowCreateModal(false);
                createForm.reset();
                createForm.setData({ unit: 'inches', stockQuantity: '0', taxTypes: [] });
            }
        });
    };

    const handleEdit = async (item) => {
        const response = await fetch(`/admin/items/${item.id}`);
        const data = await response.json();
        setSelectedItem(data);
        editForm.setData({
            name: data.name || '',
            itemCode: data.item_code || '',
            description: data.description || '',
            stockQuantity: data.stock_quantity || '0',
            unit: data.unit || 'inches',
            purchaseDate: data.purchase_date ? data.purchase_date.split('T')[0] : '',
            purchasePrice: data.purchase_price || '',
            unitPrice: data.unit_price || '',
            salesPrice: data.sales_price || '',
            manufacturer: data.manufacturer || '',
            warrantyInfo: data.warranty_info || '',
            notes: data.notes || '',
            itemCategoryId: data.category_id || '',
            taxTypes: data.tax_types?.map(tt => tt.id) || []
        });
        setShowEditModal(true);
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        editForm.put(`/admin/items/${selectedItem.id}`, {
            onSuccess: () => {
                setShowEditModal(false);
                setSelectedItem(null);
            }
        });
    };

    const handleView = async (item) => {
        const response = await fetch(`/admin/items/${item.id}`);
        const data = await response.json();
        setSelectedItem(data);
        setShowViewModal(true);
    };

    const handleDelete = (item) => {
        setSelectedItem(item);
        setShowDeleteModal(true);
    };

    const confirmDelete = () => {
        router.delete(`/admin/items/${selectedItem.id}`, {
            onSuccess: () => {
                setShowDeleteModal(false);
                setSelectedItem(null);
            }
        });
    };

    const handleStockUpdate = (item) => {
        setSelectedItem(item);
        stockForm.setData({ stockQuantity: item.stock_quantity || '0' });
        setShowStockModal(true);
    };

    const confirmStockUpdate = (e) => {
        e.preventDefault();
        stockForm.put(`/admin/items/${selectedItem.id}/stock`, {
            onSuccess: () => {
                setShowStockModal(false);
                setSelectedItem(null);
                stockForm.reset();
            }
        });
    };

    const toggleTaxType = (form, taxTypeId) => {
        const currentTypes = form.data.taxTypes || [];
        const newTypes = currentTypes.includes(taxTypeId)
            ? currentTypes.filter(id => id !== taxTypeId)
            : [...currentTypes, taxTypeId];
        form.setData('taxTypes', newTypes);
    };

    const columns = [
        { key: 'name', label: 'Title' },
        { key: 'item_code', label: 'Item Code' },
        { key: 'stock_quantity', label: 'Stock', render: (val) => val != null ? val : '0' },
        { key: 'category_title', label: 'Category' },
        { key: 'unit_price', label: 'Unit Price', render: (val) => formatCurrency(val) },
        { key: 'sales_price', label: 'Sales Price', render: (val) => val ? formatCurrency(val) : '-' },
        { key: 'manufacturer', label: 'Manufacturer' },
    ];

    const renderActions = (item) => (
        <>
            <button onClick={() => handleView(item)} className="p-2 text-gray-600 hover:text-[#2ca48b] hover:bg-gray-100 rounded-lg transition-colors">
                <EyeIcon className="w-4 h-4" />
            </button>
            <button onClick={() => handleStockUpdate(item)} className="p-2 text-gray-600 hover:text-orange-600 hover:bg-gray-100 rounded-lg transition-colors" title="Update Stock">
                <CubeIcon className="w-4 h-4" />
            </button>
            <button onClick={() => handleEdit(item)} className="p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors">
                <PencilIcon className="w-4 h-4" />
            </button>
            <button onClick={() => handleDelete(item)} className="p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors">
                <TrashIcon className="w-4 h-4" />
            </button>
        </>
    );

    const FormFields = ({ form, onSubmit, submitText }) => {
        const [activeTab, setActiveTab] = useState('basic');

        return (
            <form onSubmit={onSubmit} className="space-y-4">
                {/* Tab Navigation */}
                <div className="border-b border-gray-200">
                    <nav className="-mb-px flex space-x-8">
                        <button
                            type="button"
                            onClick={() => setActiveTab('basic')}
                            className={`py-4 px-1 border-b-2 font-medium text-sm ${
                                activeTab === 'basic'
                                    ? 'border-[#2ca48b] text-[#2ca48b]'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            }`}
                        >
                            Basic Information
                        </button>
                        <button
                            type="button"
                            onClick={() => setActiveTab('additional')}
                            className={`py-4 px-1 border-b-2 font-medium text-sm ${
                                activeTab === 'additional'
                                    ? 'border-[#2ca48b] text-[#2ca48b]'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            }`}
                        >
                            Additional Information
                        </button>
                    </nav>
                </div>

                {/* Tab Content */}
                <div className="min-h-[400px]">
                    {activeTab === 'basic' && (
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                <input type="text" value={form.data.name} onChange={e => form.setData('name', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required />
                                {form.errors.name && <p className="mt-1 text-sm text-red-600">{form.errors.name}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Item Code</label>
                                <input type="text" value={form.data.itemCode} onChange={e => form.setData('itemCode', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                                <input type="number" step="0.01" min="0" value={form.data.stockQuantity} readOnly className="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" />
                                <p className="mt-1 text-xs text-gray-500">Use Update Stock button to modify</p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                                <select value={form.data.unit} onChange={e => form.setData('unit', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]">
                                    <option value="inches">Inches</option>
                                    <option value="feet">Feet</option>
                                    <option value="meters">Meters</option>
                                    <option value="pieces">Pieces</option>
                                    <option value="units">Units</option>
                                    <option value="kg">Kg</option>
                                    <option value="lbs">Lbs</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Purchase Date</label>
                                <input type="date" value={form.data.purchaseDate} onChange={e => form.setData('purchaseDate', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select value={form.data.itemCategoryId} onChange={e => form.setData('itemCategoryId', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]">
                                    <option value="">Select category</option>
                                    {categories.map(cat => <option key={cat.id} value={cat.id}>{cat.title}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Purchase Price</label>
                                <input type="number" step="0.01" min="0" value={form.data.purchasePrice} onChange={e => form.setData('purchasePrice', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                                <input type="number" step="0.01" min="0" value={form.data.unitPrice} onChange={e => form.setData('unitPrice', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" required />
                                {form.errors.unitPrice && <p className="mt-1 text-sm text-red-600">{form.errors.unitPrice}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Sales Price</label>
                                <input type="number" step="0.01" min="0" value={form.data.salesPrice} onChange={e => form.setData('salesPrice', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                                <input type="text" value={form.data.manufacturer} onChange={e => form.setData('manufacturer', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" />
                            </div>
                        </div>
                    )}

                    {activeTab === 'additional' && (
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Tax Types</label>
                                <div className="border border-gray-300 rounded-lg p-3 max-h-48 overflow-y-auto">
                                    {taxTypes.length === 0 ? (
                                        <p className="text-sm text-gray-500">No tax types available</p>
                                    ) : (
                                        taxTypes.map(taxType => (
                                            <label key={taxType.id} className="flex items-center space-x-2 py-1">
                                                <input
                                                    type="checkbox"
                                                    checked={form.data.taxTypes?.includes(taxType.id) || false}
                                                    onChange={() => toggleTaxType(form, taxType.id)}
                                                    className="rounded border-gray-300 text-[#2ca48b] focus:ring-[#2ca48b]"
                                                />
                                                <span className="text-sm">{taxType.title} ({taxType.rate}%)</span>
                                            </label>
                                        ))
                                    )}
                                </div>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea value={form.data.description} onChange={e => form.setData('description', e.target.value)} rows="4" className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Warranty Information</label>
                                <textarea value={form.data.warrantyInfo} onChange={e => form.setData('warrantyInfo', e.target.value)} rows="3" className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea value={form.data.notes} onChange={e => form.setData('notes', e.target.value)} rows="3" className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]" />
                            </div>
                        </div>
                    )}
                </div>

                <div className="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onClick={() => { setShowCreateModal(false); setShowEditModal(false); }} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                    <button type="submit" disabled={form.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50 transition-colors">{submitText}</button>
                </div>
            </form>
        );
    };

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

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)} title="Add Item" maxWidth="6xl">
                <FormFields form={createForm} onSubmit={handleCreate} submitText="Create Item" />
            </Modal>

            <Modal show={showEditModal} onClose={() => setShowEditModal(false)} title="Edit Item" maxWidth="6xl">
                <FormFields form={editForm} onSubmit={handleUpdate} submitText="Update Item" />
            </Modal>

            <Modal show={showStockModal} onClose={() => setShowStockModal(false)} title="Update Stock" maxWidth="md">
                <form onSubmit={confirmStockUpdate} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Stock Quantity *</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            value={stockForm.data.stockQuantity}
                            onChange={e => stockForm.setData('stockQuantity', e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2ca48b]"
                            required
                        />
                        {stockForm.errors.stockQuantity && <p className="mt-1 text-sm text-red-600">{stockForm.errors.stockQuantity}</p>}
                    </div>
                    <div className="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onClick={() => setShowStockModal(false)} className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button type="submit" disabled={stockForm.processing} className="px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] disabled:opacity-50">Update Stock</button>
                    </div>
                </form>
            </Modal>

            <Modal show={showViewModal} onClose={() => setShowViewModal(false)} title="Item Details" maxWidth="2xl">
                {selectedItem && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div><span className="text-sm text-gray-500">Title</span><p className="font-medium">{selectedItem.name}</p></div>
                            <div><span className="text-sm text-gray-500">Item Code</span><p className="font-medium">{selectedItem.item_code || '-'}</p></div>
                            <div><span className="text-sm text-gray-500">Stock Quantity</span><p className="font-medium">{selectedItem.stock_quantity != null ? selectedItem.stock_quantity : '0'}</p></div>
                            <div><span className="text-sm text-gray-500">Unit</span><p className="font-medium">{selectedItem.unit || '-'}</p></div>
                            <div><span className="text-sm text-gray-500">Category</span><p className="font-medium">{selectedItem.category_title || '-'}</p></div>
                            <div><span className="text-sm text-gray-500">Purchase Date</span><p className="font-medium">{formatDate(selectedItem.purchase_date)}</p></div>
                            <div><span className="text-sm text-gray-500">Purchase Price</span><p className="font-medium">{selectedItem.purchase_price ? formatCurrency(selectedItem.purchase_price) : '-'}</p></div>
                            <div><span className="text-sm text-gray-500">Unit Price</span><p className="font-medium">{formatCurrency(selectedItem.unit_price)}</p></div>
                            <div><span className="text-sm text-gray-500">Sales Price</span><p className="font-medium">{selectedItem.sales_price ? formatCurrency(selectedItem.sales_price) : '-'}</p></div>
                            <div><span className="text-sm text-gray-500">Manufacturer</span><p className="font-medium">{selectedItem.manufacturer || '-'}</p></div>
                            <div><span className="text-sm text-gray-500">Tax Types</span><p className="font-medium">{selectedItem.tax_types?.length > 0 ? selectedItem.tax_types.map(tt => `${tt.title} (${tt.rate}%)`).join(', ') : '-'}</p></div>
                        </div>
                        <div><span className="text-sm text-gray-500">Description</span><p className="font-medium">{selectedItem.description || '-'}</p></div>
                        <div><span className="text-sm text-gray-500">Warranty Information</span><p className="font-medium">{selectedItem.warranty_info || '-'}</p></div>
                        <div><span className="text-sm text-gray-500">Notes</span><p className="font-medium">{selectedItem.notes || '-'}</p></div>
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
