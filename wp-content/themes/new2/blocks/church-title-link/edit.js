const { useBlockProps } = wp.blockEditor;

export default function Edit() {
	const blockProps = useBlockProps();
	return (
		<>
			<div {...blockProps}>
				Church Name and Denomination with Website Link
			</div>
		</>
	);
}