/**
 * サンプル02
 */
import {registerBlockType} from "@wordpress/blocks";
import {RichText, useBlockProps} from "@wordpress/block-editor";

registerBlockType('my-block/sample02', {
    edit({attributes, setAttributes}) {
        const blockProps = useBlockProps();

        // RichText: テキスト入力ができるコンポーネント
        return (
            <RichText
                {...blockProps}
                tagName='div'
                className={'sample-block-02'}
                value={attributes.content}
                allowedFormats={ [ 'core/bold', 'core/italic' ] }
                placeholder={'テキストを入力してください...'}
                onChange={(newContent) => setAttributes({content: newContent})}
            />
        );
    },
    save({attributes}) {
        const blockProps = useBlockProps.save();
        return <RichText.Content
            {...useBlockProps}
            tagName='div'
            className={'sample-block-02'}
            value={attributes.content}
        />
    },
});
