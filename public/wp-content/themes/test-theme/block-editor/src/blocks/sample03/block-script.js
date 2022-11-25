/**
 * サンプル03
 */
(function () {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { InnerBlocks, useBlockProps } = wp.blockEditor;

    registerBlockType('my-block/sample03', {
        apiVersion: 2,
        title: 'サンプル03',
        icon: 'wordpress-alt',
        category: 'text',
        supports: {
            className: false,
        },
        edit: () => {
            const blockProps = useBlockProps({
                className: 'sample-block-03',
                style:{ padding: '1em', background: '#f5f5f5' }
            });

            // 親要素(<div>)にInnerBlocksを配置しておくと、自由にブロックが設置できる
            return el('div', blockProps, el(InnerBlocks));
        },
        save: () => {
            const blockProps = useBlockProps.save({
                className: 'sample-block-03',
                style: { padding: '1em', background: '#f5f5f5' }
            });

            // save側は InnerBlocks.Content になることに注意
            return el('div', blockProps, el(InnerBlocks.Content) );
        },
    });
})();
