import { PrinterIcon, ArrowDownTrayIcon } from '@heroicons/react/24/outline';
import { useState } from 'react';

export default function PrintButton({ pdfUrl, excelUrl, invoiceNumber = '', className = '' }) {
    const [isOpen, setIsOpen] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    const handleExport = async (url, type) => {
        if (!url) return;
        
        setIsLoading(true);
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Export failed');
            
            const blob = await response.blob();
            const downloadUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = downloadUrl;
            
            const extension = type === 'pdf' ? 'pdf' : 'xlsx';
            link.download = `${invoiceNumber || 'document'}-${new Date().toISOString().split('T')[0]}.${extension}`;
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(downloadUrl);
        } catch (error) {
            console.error('Export error:', error);
            alert('Failed to export. Please try again.');
        } finally {
            setIsLoading(false);
            setIsOpen(false);
        }
    };

    return (
        <div className={`relative inline-block ${className}`}>
            <button
                onClick={() => setIsOpen(!isOpen)}
                disabled={isLoading}
                className="flex items-center px-3 py-2 text-gray-600 hover:text-[#2ca48b] hover:bg-gray-100 rounded-lg transition-colors disabled:opacity-50"
                title="Print/Export"
            >
                <PrinterIcon className="w-5 h-5" />
            </button>

            {isOpen && (
                <>
                    <div
                        className="fixed inset-0 z-10"
                        onClick={() => setIsOpen(false)}
                    />
                    <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-20 border border-gray-200">
                        <div className="py-1">
                            <button
                                onClick={() => handleExport(pdfUrl, 'pdf')}
                                disabled={isLoading || !pdfUrl}
                                className="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <PrinterIcon className="w-4 h-4 mr-2 text-red-500" />
                                {isLoading ? 'Exporting...' : 'Print PDF'}
                            </button>
                            <button
                                onClick={() => handleExport(excelUrl, 'excel')}
                                disabled={isLoading || !excelUrl}
                                className="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <ArrowDownTrayIcon className="w-4 h-4 mr-2 text-green-500" />
                                {isLoading ? 'Exporting...' : 'Export Excel'}
                            </button>
                        </div>
                    </div>
                </>
            )}
        </div>
    );
}

