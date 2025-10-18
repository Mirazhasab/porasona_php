document.addEventListener('click', function (event) {
    const button = event.target.closest('[data-highlight-color]');
    if (!button) {
        return;
    }

    event.preventDefault();

    const form = button.closest('[data-note-highlight-form]');
    if (!form) {
        return;
    }

    const textarea = form.querySelector('textarea[name="content"]');
    if (!textarea) {
        return;
    }

    const color = button.dataset.highlightColor;
    if (!color) {
        return;
    }

    const start = textarea.selectionStart || 0;
    const end = textarea.selectionEnd || 0;
    const value = textarea.value || '';
    const openingTag = '[[hl:' + color + ']]';
    const closingTag = '[[/hl]]';

    const selectedText = start !== end
        ? value.slice(start, end)
        : 'Highlight this text';

    const before = value.slice(0, start);
    const after = value.slice(end);
    const updatedValue = before + openingTag + selectedText + closingTag + after;

    textarea.value = updatedValue;
    textarea.dispatchEvent(new Event('input', { bubbles: true }));

    const selectionStart = before.length + openingTag.length;
    const selectionEnd = selectionStart + selectedText.length;

    setTimeout(function () {
        textarea.focus();
        textarea.setSelectionRange(selectionStart, selectionEnd);
    }, 0);
});
