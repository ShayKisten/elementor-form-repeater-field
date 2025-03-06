jQuery(document).ready(function($) {
    var originalContentCache = {};
    var originalTitleCache = {};

    console.log('Script initialized');

    function initializeTitles() {
        console.log('initializeTitles started');
        $('.efrf-repeater-start').each(function() {
            var $startGroup = $(this).closest('.elementor-field-group');
            var identifier = $(this).data('repeater-identifier');
            var $endGroup = $('.efrf-repeater-end[data-repeater-identifier="' + identifier + '"]').closest('.elementor-field-group');
            var $originalContent = $startGroup.nextUntil($endGroup);
            console.log('Processing repeater-start for identifier:', identifier);

            if ($originalContent.length > 0 && !originalContentCache[identifier]) {
                originalContentCache[identifier] = $originalContent.clone(true);
                originalTitleCache[identifier] = $startGroup.find('.elementor-field-label').text().trim() || 'Section';
                console.log('Cached original content for', identifier, originalContentCache[identifier].length);
                console.log('Cached original title for', identifier, originalTitleCache[identifier]);

                var $section = $('<div class="efrf-section efrf-expanded elementor-column elementor-col-100" data-repeater-identifier="' + identifier + '"></div>').insertAfter($startGroup);
                $section.append('<div class="efrf-section-header"><label class="elementor-field-label">' + originalTitleCache[identifier] + ' 1</label><div class="efrf-controls"><span class="efrf-toggle">▼</span></div></div>');
                
                console.log('Appending hidden field for', identifier);
                $section.append('<input type="hidden" name="form_fields[repeater_' + identifier + '_0]" value="--' + originalTitleCache[identifier] + ' 1--" id="form-field-repeater_' + identifier + '_0">');
                console.log('Hidden field appended');

                $originalContent.detach().appendTo($section);

                renameFields($section, identifier, 0);

                initializeNestedRepeaters($section);
            }
        });
        console.log('initializeTitles completed');
    }

    function renameFields($section, identifier, index) {
        $section.find('input, textarea, select').filter('[id^="form-field-"]').each(function() {
            var $input = $(this);
            var fieldId = $input.attr('id').replace('form-field-', '').toLowerCase();
            fieldId = fieldId.replace(/_\d+$/, '');

            var $currentSection = $input.closest('.efrf-section');
            var parents = [];
            var parentIndices = [];

            var $parentSection = $currentSection;
            while ($parentSection.parent().closest('.efrf-section').length) {
                $parentSection = $parentSection.parent().closest('.efrf-section');
                parents.unshift($parentSection.data('repeater-identifier'));
                parentIndices.unshift($parentSection.parent().find('.efrf-section[data-repeater-identifier="' + $parentSection.data('repeater-identifier') + '"]').index($parentSection));
            }

            var fieldNameSuffix = '';
            var fieldIdSuffix = '';

            if (parents.length === 0) {
                fieldNameSuffix = '_' + index;
                fieldIdSuffix = '_' + index;
            } else {
                fieldNameSuffix = '_' + index;
                fieldIdSuffix = '_' + index;
                for (var i = 0; i < parents.length; i++) {
                    fieldNameSuffix += '_' + parents[i] + '_' + parentIndices[i];
                    fieldIdSuffix += '_' + parents[i] + '_' + parentIndices[i];
                }
            }

            var newFieldName = 'form_fields[' + fieldId + fieldNameSuffix + ']';
            var newFieldId = 'form-field-' + fieldId + fieldIdSuffix;

            $input.attr('name', newFieldName);
            $input.attr('id', newFieldId);
            console.log('Renamed input:', fieldId, 'to name:', newFieldName, 'and id:', newFieldId);
        });
    }

    function initializeNestedRepeaters($section) {
        $section.find('.efrf-repeater-start').each(function() {
            var $nestedStart = $(this);
            var nestedIdentifier = $nestedStart.data('repeater-identifier');
            var $nestedEnd = $section.find('.efrf-repeater-end[data-repeater-identifier="' + nestedIdentifier + '"]');
            var $nestedContent = $nestedStart.closest('.elementor-field-group').nextUntil($nestedEnd.closest('.elementor-field-group'));

            if ($nestedContent.length > 0 && !originalContentCache[nestedIdentifier]) {
                originalContentCache[nestedIdentifier] = $nestedContent.clone(true);
                originalTitleCache[nestedIdentifier] = $nestedStart.closest('.elementor-field-group').find('.elementor-field-label').text().trim() || 'Nested Section';
                console.log('Cached nested content for', nestedIdentifier);
            }

            var $nestedSection = $('<div class="efrf-section efrf-expanded elementor-column elementor-col-100" data-repeater-identifier="' + nestedIdentifier + '"></div>').insertAfter($nestedStart.closest('.elementor-field-group'));
            $nestedSection.append('<div class="efrf-section-header"><label class="elementor-field-label">' + originalTitleCache[nestedIdentifier] + ' 1</label><div class="efrf-controls"><span class="efrf-toggle">▼</span></div></div>');
            $nestedSection.append('<input type="hidden" name="form_fields[repeater_' + nestedIdentifier + '_0]" value="--' + originalTitleCache[nestedIdentifier] + ' 1--" id="form-field-repeater_' + nestedIdentifier + '_0">');
            $nestedContent.detach().appendTo($nestedSection);

            renameFields($nestedSection, nestedIdentifier, 0);
            initializeNestedRepeaters($nestedSection);
        });
    }

    initializeTitles();

    $(document).on('click', '.efrf-toggle', function() {
        console.log('Toggle clicked');
        var $header = $(this).closest('.efrf-section-header');
        var $section = $header.closest('.efrf-section');
        if ($section.hasClass('efrf-expanded')) {
            $section.removeClass('efrf-expanded').addClass('efrf-collapsed');
            $(this).text('▶');
        } else {
            $section.removeClass('efrf-collapsed').addClass('efrf-expanded');
            $(this).text('▼');
        }
    });

    $(document).on('click', '.efrf-add-more', function() {
        console.log('Add More clicked');
        var $button = $(this);
        var identifier = $button.data('repeater-identifier');
        var $endGroup = $button.closest('.elementor-field-group');
        var $parentSection = $button.closest('.efrf-section');

        var $originalContent = originalContentCache[identifier];
        var originalTitle = originalTitleCache[identifier];

        console.log('Add More clicked for identifier:', identifier);
        console.log('Cloning original content:', $originalContent.length);
        console.log('Using title:', originalTitle);

        if ($originalContent.length === 0) {
            console.error('No original content found for identifier:', identifier);
            return;
        }

        var $allSections = $parentSection.length
            ? $parentSection.find('.efrf-section[data-repeater-identifier="' + identifier + '"]')
            : $('.efrf-section[data-repeater-identifier="' + identifier + '"]');
        var sectionCount = $allSections.length;

        var $newSection = $('<div class="efrf-section efrf-expanded elementor-column elementor-col-100" data-repeater-identifier="' + identifier + '"></div>').insertBefore($endGroup);
        var controlsHtml = sectionCount === 0 && !$parentSection.length ? '<span class="efrf-toggle">▼</span>' : '<span class="efrf-toggle">▼</span><span class="efrf-delete">✕</span>';
        $newSection.append('<div class="efrf-section-header"><label class="elementor-field-label">' + originalTitle + ' ' + (sectionCount + 1) + '</label><div class="efrf-controls">' + controlsHtml + '</div></div>');
        $newSection.append('<input type="hidden" name="form_fields[repeater_' + identifier + '_' + sectionCount + ']" value="--' + originalTitle + ' ' + (sectionCount + 1) + '--" id="form-field-repeater_' + identifier + '_' + sectionCount + '">');
        $originalContent.clone(true).appendTo($newSection);

        renameFields($newSection, identifier, sectionCount);

        initializeNestedRepeaters($newSection);
    });

    $(document).on('click', '.efrf-delete', function() {
        console.log('Delete clicked');
        var $section = $(this).closest('.efrf-section');
        var identifier = $section.data('repeater-identifier');
        var $parentSection = $section.closest('.efrf-section[data-repeater-identifier]');
        var $remainingSections = $parentSection.length 
            ? $parentSection.find('.efrf-section[data-repeater-identifier="' + identifier + '"]') 
            : $('.efrf-section[data-repeater-identifier="' + identifier + '"]');

        $section.remove();

        console.log('Delete clicked for identifier:', identifier);

        var originalTitle = originalTitleCache[identifier] || 'Section';
        $remainingSections.each(function(index) {
            var sectionNumber = index + 1;
            var $label = $(this).find('.elementor-field-label');
            if ($label.length) {
                $label.text(originalTitle + ' ' + sectionNumber);
            }
            $(this).find('input[type="hidden"]').attr('name', 'form_fields[repeater_' + identifier + '_' + index + ']')
                .attr('id', 'form-field-repeater_' + identifier + '_' + index)
                .val('--' + originalTitle + ' ' + sectionNumber + '--');
            renameFields($(this), identifier, index);
        });
    });
});