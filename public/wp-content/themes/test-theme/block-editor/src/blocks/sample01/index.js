/**
 * WordPress dependencies
 */
import {registerBlockType} from '@wordpress/blocks';
import {useBlockProps} from '@wordpress/block-editor';

// Register the block
registerBlockType('my-block/sample01', {
    edit: function () {
        // ブロックに付与する属性
        const blockProps = useBlockProps({
            style: {color: '#fff', backgroundColor: 'blue'}
        });
        // 出力したいHTMLを返す
        return (
            <div {...blockProps}>サンプル01ブロックだよ</div>
        );
    },
    save: function () {
        // ブロックに付与する属性
        const blockProps = useBlockProps.save({
            style: {color: '#fff', backgroundColor: 'green'}
        });
        // 出力したいHTMLを返す
        return <div {...blockProps}>サンプル01ブロックだよ' )</div>;
    },
});
