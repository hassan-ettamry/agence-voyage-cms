window.BuilderRichTextControls = {

    editorFor(button) {

        const panel =
            button.closest('#settings-panel');

        return panel
            ?.querySelector('[data-richtext-editor="true"]')
            || null;

    },

    run(button) {

        const editor =
            this.editorFor(button);

        if (!editor) return;

        const rawCommand =
            button.dataset.richtextCommand || '';

        const [command, commandValue] =
            rawCommand.split(':');

        let value =
            button.dataset.richtextValue || commandValue || null;

        editor.focus();

        if (command === 'createLink') {
            value = window.prompt('URL', 'https://') || null;
        }

        if (command === 'insertImage') {
            value = window.prompt('Image URL', '') || null;
        }

        if (command === 'insertVideo') {
            value = window.prompt('Video URL', '') || null;
        }

        if (command === 'table') {
            this.insertHtml(editor, '<table><tbody><tr><td>Cell</td><td>Cell</td></tr></tbody></table>');
            return;
        }

        if (command === 'insertChecklist') {
            this.insertHtml(editor, '<ul><li>Checklist item</li></ul>');
            return;
        }

        if (command === 'blockquote') {
            document.execCommand('formatBlock', false, 'blockquote');
            this.sync(editor);
            return;
        }

        if (command === 'code') {
            this.insertHtml(editor, '<code>code</code>');
            return;
        }

        if (command === 'audio') {
            this.insertHtml(editor, '<span>Audio</span>');
            return;
        }

        if (!value && ['createLink', 'insertImage', 'insertVideo'].includes(command)) {
            return;
        }

        document.execCommand(command, false, value);

        this.sync(editor);

    },

    insertHtml(editor, html) {

        editor.focus();
        document.execCommand('insertHTML', false, html);
        this.sync(editor);

    },

    sync(editor) {

        const nodeId =
            editor.dataset.targetNodeId;

        const field =
            editor.dataset.settingField;

        if (!nodeId || !field) return;

        BuilderSettingsUpdater.updateField(
            nodeId,
            field,
            editor.innerHTML
        );

    }

};
