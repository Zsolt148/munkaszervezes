async function getFeedItems(query) {

    const response = await axios.get(`/admins-search?search=${query}`);

    let users = response.data.map(user => {
        user.userId = user.id
        user.email = user.email
        user.id = '@' + user.id
        user.text = '@' + user.name

        return user
    })

    return users.slice(0, 10)
}

function customItemRenderer(item) {

    const itemElement = document.createElement('span');

    itemElement.classList.add('custom-item');
    itemElement.setAttribute('data-mention', item.id)
    itemElement.setAttribute('data-user-id', item.userId)
    itemElement.textContent = `${ item.text } `;

    // const usernameElement = document.createElement('span');
    // usernameElement.classList.add('custom-item-username');
    // usernameElement.textContent = item.email;
    // itemElement.appendChild(usernameElement);

    return itemElement;
}

function MentionCustomization(editor) {
    // The upcast converter will convert <span class="mention" data-user-id="">
    // elements to the model 'mention' attribute.
    editor.conversion.for('upcast').elementToAttribute({
        view: {
            name: 'span',
            key: 'data-mention',
            classes: 'mention',
            attributes: {
                'data-user-id': true
            }
        },
        model: {
            key: 'mention',
            value: viewItem => {
                // The mention feature expects that the mention attribute value
                // in the model is a plain object with a set of additional attributes.
                // In order to create a proper object, use the toMentionAttribute helper method:
                const mentionAttribute = editor.plugins.get('Mention').toMentionAttribute(viewItem, {
                    // Add any other properties that you need.
                    userId: viewItem.getAttribute('data-user-id')
                } );

                return mentionAttribute;
            }
        },
        converterPriority: 'high'
    });

    // Downcast the model 'mention' text attribute to a view <span> element.
    editor.conversion.for('downcast').attributeToElement({
        model: 'mention',
        view: (modelAttributeValue, { writer }) => {
            // Do not convert empty attributes (lack of value means no mention).
            if (!modelAttributeValue) {
                return;
            }

            return writer.createAttributeElement('span', {
                class: 'mention',
                'data-mention': modelAttributeValue.id,
                'data-user-id': modelAttributeValue.userId,
            }, {
                // Make mention attribute to be wrapped by other attribute elements.
                priority: 20,
                // Prevent merging mentions together.
                id: modelAttributeValue.uid
            } );
        },
        converterPriority: 'high'
    });
}

export { getFeedItems, customItemRenderer, MentionCustomization }
