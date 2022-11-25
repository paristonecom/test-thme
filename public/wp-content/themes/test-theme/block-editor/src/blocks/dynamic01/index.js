/**
 * サンプル02
 */
import {registerBlockType} from "@wordpress/blocks";
import {useBlockProps} from "@wordpress/block-editor";
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType('my-block/dynamic01', {
    edit: () => {
        const blockProps = useBlockProps();

        // RichText: テキスト入力ができるコンポーネント
        return (
            <div {...blockProps}>
                <ServerSideRender
                    block = "my-block/dynamic01"
                />
            </div>
        );
    },
});
