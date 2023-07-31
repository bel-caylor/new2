const { useBlockProps } = wp.blockEditor;
// const { serverSideRender } = wp.serverSideRender;

export default function Edit() {
	const blockProps = useBlockProps();
	// const serverSideRender = serverSideRender();
	return (
		<>
			<div {...blockProps}>
				Business Name with Website Link
				{/* <serverSideRender
					block="new2/business-title-link"
					attributes={ props.attributes }
				/> */}
			</div>
		</>
	);
}