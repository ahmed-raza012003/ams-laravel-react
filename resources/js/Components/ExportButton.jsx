import { useState } from 'react';
import { PrinterIcon, ArrowDownTrayIcon } from '@heroicons/react/24/outline';

export default function ExportButton({ pdfUrl, excelUrl, isLoading = false, className = '' }) {
    const [isOpen, setIsOpen] = useState(false);

    const handleExport = (url) => {
        if (!url) return;
        
        // Trigger download directly
        window.location.href = url;
        setIsOpen(false);
    };

    return (
        <div className={`relative ${className}`}>
            <button
                onClick={() => setIsOpen(!isOpen)}
                disabled={isLoading}
                className="flex items-center px-4 py-2 bg-[#2ca48b] text-white rounded-lg hover:bg-[#238b74] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <ArrowDownTrayIcon className="w-5 h-5 mr-2" />
                Export
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
                                onClick={() => handleExport(pdfUrl)}
                                className="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                            >
                                <PrinterIcon className="w-4 h-4 mr-2 text-red-500" />
                                Export as PDF
                            </button>
                            <button
                                onClick={() => handleExport(excelUrl)}
                                className="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                            >
                                <ArrowDownTrayIcon className="w-4 h-4 mr-2 text-green-500" />
                                Export as Excel
                            </button>
                        </div>
                    </div>
                </>
            )}
        </div>
    );
}

