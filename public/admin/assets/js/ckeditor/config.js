/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {
// Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
    config.toolbar = [
        {
            name: 'basicstyles',
            groups: ['basicstyles', 'cleanup'],
            items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
        },
        {
            name: 'clipboard',
            groups: ['clipboard', 'undo'],
            items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
        },
        {
            name: 'editing',
            groups: ['find', 'selection'],
            items: ['Find', 'Replace', '-', 'SelectAll']
        },
        {
            name: 'paragraph',
            groups: ['list', 'indent', 'blocks', 'align'],
            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
        },
        {
            name: 'document',
            groups: ['mode', 'doctools'],
            items: ['Source', '-', 'Templates']
        },
        '/',
        {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
        {
            name: 'insert',
            items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak', 'Iframe']
        },
        {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
        {name: 'colors', items: ['TextColor', 'BGColor']},
        {name: 'tools', items: [ 'ShowBlocks']},
        {name: 'others', items: ['-']},
    ];
    config.removeButtons = 'Underline,Subscript,Superscript';
    config.height = 500;

};
