const { useBlockProps } = wp.blockEditor;

export default function Edit() {

	return (
		<>
            <div {...useBlockProps()}>
				Business Title with Website Link
			</div>
		</>
	);
}