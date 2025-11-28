export default function Checkbox({ className = '', ...props }) {
    return (
        <input
            {...props}
            type="checkbox"
            className={
                'rounded border-gray-300 text-[#2ca48b] shadow-sm focus:ring-[#2ca48b] ' +
                className
            }
        />
    );
}
