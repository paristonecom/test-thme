import {registerBlockType} from "@wordpress/blocks";
import {RichText, useBlockProps, useInnerBlocksProps} from "@wordpress/block-editor";

registerBlockType('my-block/sample05', {
    edit: ({attributes, setAttributes}) => {
        // ブロックの属性値
        const blockProps = useBlockProps({
            className: 'sample-block-05',
        });

        // RichTextの属性値
        const richTextProps = {
            tagName: 'div',
            value: attributes.content,
            className: 'sample-block-05__title',
            placeholder: 'テキストを入力してください...',
            onChange: (newContent) => {
                setAttributes({content: newContent});
            }
        };

        // インナーブロックの属性値
        const innerBlocksProps = useInnerBlocksProps(
            {className: 'sample-block-05__body'},
            {templateLock: false}
        );

        // divの中にRichTextとインナーブロックを入れる
        return (
            <div {...blockProps}>
                <RichText {...richTextProps}/>
                <div {...innerBlocksProps}>
                </div>
            </div>
        );
    },
    save: ({attributes}) => {
        // ブロックの属性値
        const blockProps = useBlockProps.save({
            className: 'sample-block-05',
        });

        // RichTextの属性値
        const richTextProps = {
            tagName: 'div',
            value: attributes.content,
            className: 'sample-block-05__title',
        };

        // インナーブロックの属性値
        const innerBlocksProps = useInnerBlocksProps.save({
            className: 'sample-block-05__body',
        });

        return (
            <div {...blockProps}>
                <RichText.Content
                    {...richTextProps}
                />
                <div {...innerBlocksProps}>
                </div>
            </div>
        );
    },
});
