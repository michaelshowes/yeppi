/* globals tinymce, tinyMCE */
jQuery(document).ready(function() {

  tinymce.create('tinymce.plugins.highlight_callout_plugin', {
    init(editor) {
      editor.addCommand('add_callout_shortcode', function() {
        const selected = tinyMCE.activeEditor.selection.getContent();

        let content;

        if (selected) {
          content = '[highlight-callout]' + selected + '[/highlight-callout]';
        } else {
          content = '[highlight-callout][/highlight-callout]';
        }

        tinymce.execCommand('mceInsertContent', false, content);
      });

      // Register buttons - trigger above command when clicked
      editor.addButton('highlight_callout_button', {
        title: 'Insert Highlight Callout',
        cmd: 'add_callout_shortcode',
        icon: 'insert-callout'
      });
    }
  });

  tinymce.PluginManager.add('highlight_callout_button', tinymce.plugins.highlight_callout_plugin);
});
