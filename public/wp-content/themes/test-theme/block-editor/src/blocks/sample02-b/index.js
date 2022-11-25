/**
 * サンプル02-B
 */
import {registerBlockType} from "@wordpress/blocks";
import {RichText, useBlockProps} from "@wordpress/block-editor";
import {SelectControl} from '@wordpress/components';
import {Fragment} from "@wordpress/element";

// 選択可能な色
const COLORES = [
    {value: '', label: 'カラーを選択'},
    {value: 'red', label: '赤色'},
    {value: 'blue', label: '青色'}
];

registerBlockType('my-block/sample02-b', {
    edit: ({attributes, setAttributes}) => {
        const blockProps = useBlockProps();

        // RichText: テキスト入力ができるコンポーネント
        return (
            <Fragment>
                <RichText
                    {...blockProps}
                    tagName='div'
                    className={'sample-block-02--b'}
                    style={{'color': attributes.color || null}}
                    value={attributes.content}
                    allowedFormats={['core/bold', 'core/italic']}
                    placeholder={'テキストを入力してください...'}
                    onChange={(newContent) => setAttributes({content: newContent})}
                />
                <SelectControl
                    value={attributes.color}
                    onChange={(newContent) => {
                        setAttributes({color: newContent});
                    }}
                    options={COLORES}
                />
            </Fragment>
        );
    },
    save: ({attributes}) => {
        const blockProps = useBlockProps.save();
        return <RichText.Content
            {...useBlockProps}
            tagName='div'
            className={'sample-block-02--b'}
            value={attributes.content}
            style={{'color': attributes.color || null}}
        />
    },
});
