(() => {
  // theme/blocks/business-loop/edit.js
  var { useBlockProps } = wp.blockEditor;
  function Edit() {
    return /* @__PURE__ */ React.createElement(React.Fragment, null, /* @__PURE__ */ React.createElement("div", { ...useBlockProps() }, "Business Title with Website Link"));
  }

  // theme/blocks/business-loop/save.js
  function save() {
    return null;
  }

  // theme/blocks/business-loop/index.js
  var { registerBlockType } = wp.blocks;
  registerBlockType("new2/business-loop", {
    title: "Business Loop",
    description: "Business Query Loop",
    icon: "megaphone",
    keywords: "query business directory",
    attributes: {
      pageType: {
        type: "string"
      }
    },
    edit: Edit,
    save
  });

  // theme/blocks/business-title-link/edit.js
  var { useBlockProps: useBlockProps2 } = wp.blockEditor;
  function Edit2() {
    const blockProps = useBlockProps2();
    return /* @__PURE__ */ React.createElement(React.Fragment, null, /* @__PURE__ */ React.createElement("div", { ...blockProps }, "Business Name with Website Link"));
  }

  // theme/blocks/business-title-link/save.js
  function save2() {
    return null;
  }

  // theme/blocks/business-title-link/index.js
  var { registerBlockType: registerBlockType2 } = wp.blocks;
  registerBlockType2("new2/business-title-link", {
    title: "Business Title with Link",
    description: "Business Title with Website Link",
    icon: "heading",
    keywords: "business title link",
    edit: Edit2,
    save: save2
  });

  // theme/blocks/church-title-link/edit.js
  var { useBlockProps: useBlockProps3 } = wp.blockEditor;
  function Edit3() {
    const blockProps = useBlockProps3();
    return /* @__PURE__ */ React.createElement(React.Fragment, null, /* @__PURE__ */ React.createElement("div", { ...blockProps }, "Church Name and Denomination with Website Link"));
  }

  // theme/blocks/church-title-link/save.js
  function save3() {
    return null;
  }

  // theme/blocks/church-title-link/index.js
  var { registerBlockType: registerBlockType3 } = wp.blocks;
  registerBlockType3("new2/church-title-link", {
    title: "Church Title with Link",
    description: "Church Title & Denomination with Website Link",
    icon: "heading",
    keywords: "church title link",
    edit: Edit3,
    save: save3
  });
})();
