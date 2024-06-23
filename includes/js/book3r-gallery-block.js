const { registerBlockType } = wp.blocks;
const { SelectControl } = wp.components;
const { InspectorControls } = wp.blockEditor;
const { withSelect } = wp.data;

registerBlockType('book3r/gallery', {
    title: 'Book3r Galerie',
    icon: 'format-gallery',
    category: 'common',
    attributes: {
        galleryId: {
            type: 'number',
            default: 0
        }
    },
    edit: withSelect(select => {
        const galleries = select('core').getEntityRecords('postType', 'book3r_gallery');
        console.log(galleries); // Check if galleries are being fetched
        return {
            galleries: galleries
        };
    })(({ galleries, attributes, setAttributes }) => {
        const galleryOptions = galleries
            ? galleries.map(gallery => ({
                  label: gallery.title.rendered,
                  value: gallery.id
              }))
            : [];

        return wp.element.createElement(
            'div',
            {},
            wp.element.createElement(
                InspectorControls,
                {},
                wp.element.createElement(SelectControl, {
                    label: 'Galerie auswählen',
                    value: attributes.galleryId,
                    options: [{ label: 'Wähle eine Galerie', value: 0 }, ...galleryOptions],
                    onChange: (value) => setAttributes({ galleryId: parseInt(value) })
                })
            ),
            wp.element.createElement(
                'div',
                {},
                attributes.galleryId
                    ? wp.element.createElement('p', {}, 'Galerie ID: ' + attributes.galleryId)
                    : wp.element.createElement('p', {}, 'Wähle eine Galerie aus.')
            )
        );
    }),
    save() {
        return null;
    }
});
