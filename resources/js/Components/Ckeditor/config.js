// The editor creator to use.
import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment.js';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat.js';
import AutoImage from '@ckeditor/ckeditor5-image/src/autoimage.js';
import AutoLink from '@ckeditor/ckeditor5-link/src/autolink.js';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote.js';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold.js';
import CloudServices from '@ckeditor/ckeditor5-cloud-services/src/cloudservices.js';
// import Code from '@ckeditor/ckeditor5-basic-styles/src/code.js';
// import CodeBlock from '@ckeditor/ckeditor5-code-block/src/codeblock.js';
import DataFilter from '@ckeditor/ckeditor5-html-support/src/datafilter.js';
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials.js';
import { Font } from '@ckeditor/ckeditor5-font';
import GeneralHtmlSupport from '@ckeditor/ckeditor5-html-support/src/generalhtmlsupport.js';
import SourceEditing from '@ckeditor/ckeditor5-source-editing/src/sourceediting';
import Heading from '@ckeditor/ckeditor5-heading/src/heading.js';
import Highlight from '@ckeditor/ckeditor5-highlight/src/highlight.js';
import HorizontalLine from '@ckeditor/ckeditor5-horizontal-line/src/horizontalline.js';
import Image from '@ckeditor/ckeditor5-image/src/image.js';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption.js';
import ImageInsert from '@ckeditor/ckeditor5-image/src/imageinsert.js';
import ImageResize from '@ckeditor/ckeditor5-image/src/imageresize.js';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle.js';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar.js';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload.js';
import Indent from '@ckeditor/ckeditor5-indent/src/indent.js';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic.js';
import Underline from '@ckeditor/ckeditor5-basic-styles/src/underline';
import Strikethrough from '@ckeditor/ckeditor5-basic-styles/src/strikethrough';
import Link from '@ckeditor/ckeditor5-link/src/link.js';
import LinkImage from '@ckeditor/ckeditor5-link/src/linkimage.js';
import List from '@ckeditor/ckeditor5-list/src/list.js';
import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed.js';
import Mention from '@ckeditor/ckeditor5-mention/src/mention.js';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph.js';
import PasteFromOffice from '@ckeditor/ckeditor5-paste-from-office/src/pastefromoffice.js';
import { Table, TableCellProperties, TableProperties, TableToolbar } from '@ckeditor/ckeditor5-table';
import TextTransformation from '@ckeditor/ckeditor5-typing/src/texttransformation.js';

import UploadAdapterPlugin from "./UploadAdapter";
import { getFeedItems, customItemRenderer, MentionCustomization } from "./Mention";

export default class ClassicEditor extends ClassicEditorBase {}

// Plugins to include in the build.
ClassicEditor.builtinPlugins = [
    Alignment,
    Autoformat,
    AutoImage,
    AutoLink,
    BlockQuote,
    Bold,
    Underline,
    Strikethrough,
    CloudServices,
    // Code,
    // CodeBlock,
    DataFilter,
    Essentials,
    Font,
    GeneralHtmlSupport,
    SourceEditing,
    Heading,
    Highlight,
    HorizontalLine,
    Image,
    ImageCaption,
    ImageInsert,
    ImageResize,
    ImageStyle,
    ImageToolbar,
    ImageUpload,
    Indent,
    Italic,
    Link,
    LinkImage,
    List,
    MediaEmbed,
    Paragraph,
    PasteFromOffice,
    Table,
    TableToolbar,
    TableProperties,
    TableCellProperties,
    TextTransformation,
    Mention,
    MentionCustomization,
    UploadAdapterPlugin
];

// Editor configuration.
ClassicEditor.defaultConfig = {
    toolbar: {
        items: [
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            // 'strikethrough',
            // 'underline',
            'fontSize',
            'fontFamily',
            'fontColor',
            'FontBackgroundColor',
            'highlight',
            '|',
            'alignment',
            'outdent',
            'indent',
            'bulletedList',
            'numberedList',
            'horizontalLine',
            '|',
            // 'code',
            // 'codeBlock',
            '|',
            // '-',
            'link',
            // 'imageUpload',
            'imageInsert',
            'blockQuote',
            'insertTable',
            'mediaEmbed',
            'sourceEditing',
            'undo',
            'redo',
        ]
    },
    image: {
        toolbar: [
            'imageTextAlternative',
            'toggleImageCaption',
            'imageStyle:inline',
            'imageStyle:block',
            'imageStyle:side',
            'linkImage'
        ]
    },
    table: {
        contentToolbar: [
            'tableColumn',
            'tableRow',
            'mergeTableCells',
            'tableProperties',
            'tableCellProperties'
        ],

        tableProperties: {
            // Configuration of the TableProperties plugin.
            // ...
        },

        tableCellProperties: {
            // Configuration of the TableCellProperties plugin.
            // ...
        }
    },
    htmlSupport: {
        allow: [
            {
                name: /.*/,
                attributes: true,
                classes: true,
                styles: true
            }
        ]
    },
    mention: {
        feeds: [
            {
                marker: '@',
                feed: getFeedItems,
                itemRenderer: customItemRenderer,
                minimumCharacters: 1
            }
        ]
    },
    // This value must be kept in sync with the language defined in webpack.config.js.
    language: 'hu'
};
