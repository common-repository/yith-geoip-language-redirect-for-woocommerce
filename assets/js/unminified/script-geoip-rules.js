/* Yith GeoIP Language Redirect for WooCommerce*/

jQuery(document).ready(function ($) {

    main_init();

    function main_init() {

        $('#_add_rule_row').click(add_rule);

        if (0 < $('.select_type').length) {
            $('#yith_wcgeoip_rules_panel').on('keyup', '.finder', function () {
                var $select_type = $(this).closest('td').prev('.option_select').find('.select_type');
                $selected_type = $select_type.val(),
                    $type = $select_type.find('option:selected').data('type');
                if ($selected_type != 'custom_url' & $selected_type != 'custom_url_regex') {
                    $(this).autocomplete(
                        {
                            minLength: 3,
                            source   : function (request, response) {
                                request['select'] = $selected_type;
                                request['type'] = $type;
                                finder_source(request, response);
                            },
                            select   : function (event, ui) {

                                $(this).val(ui.item.permalink);

                                return false;
                            }
                        }
                    ).autocomplete("instance")._renderItem = function (ul, item) {

                        var $li = $('<li>');
                        var $div = $('<div>');

                        var title = $('<b>').append(item.title);
                        var permalink = $('<i>').append($('<small>').append(' (' + item.permalink + ')'));

                        title.appendTo($div);
                        permalink.appendTo($div);

                        $div.appendTo($li);
                        return $li.appendTo(ul);
                    };
                }
            });
        }

        $('#yith_wcgeoip_rules_panel').on('change', '.select_type', function () {
            var $rule_row = $(this).closest('.yith_wcgeoip_rule_row');
            var $option_selected = $(this).find('option:selected');
            var $form_field = $(this).closest('.form-field').next('.form-field');

            if ($option_selected.hasClass('all') | $option_selected.hasClass('archive')) {
                $form_field.find('.finder').prop('readonly', true);
                $form_field.find('.finder').val($option_selected.data('placeholder'));
            } else {
                $form_field.find('.finder').prop('readonly', false);
                $form_field.find('.finder').val('');
            }

            if ($(this).hasClass('origin')) {
                var $destination_select = $rule_row.find('.option_select>.destination');
                var $destination_finder = $rule_row.find('.option-destinantion>.finder').first();

                var $data_type = $option_selected.data('type');

                if ('_custom_type' == $data_type | $option_selected.hasClass('all') | $option_selected.hasClass('archive')) {
                    $destination_select.val('custom_url');
                    $destination_finder.val($option_selected.data('placeholder'));
                } else {
                    $destination_select.val($(this).val());
                    $destination_select.trigger('change');

                }
            }

        });

        $('#yith_wcgeoip_rules_panel').on('change', '._rule', function () {
            var $_data_to_save = $('#_data_to_save'),
                $_id = $(this).closest('.yith_wcgeoip_rule_row').attr('id');

            if ($_data_to_save.val().split(',').indexOf($_id) < 0) {
                if (0 == $_data_to_save.val().length) {
                    $_data_to_save.val($_id);
                } else {
                    $_data_to_save.val($_data_to_save.val() + ',' + $_id);
                }
            }
            $('#_data_to_save').val($('#_data_to_save').val().replace(',,', ','));
        });

        $('.origin').each(function () {
            var $option_selected = $(this).find('option:selected');
            var $form_field = $(this).closest('.form-field');

            if ($option_selected.hasClass('all') | $option_selected.hasClass('archive')) {
                $form_field.find('.finder').prop('readonly', true);
            }
        });

        $('#_save_rules').click(function (event) {
            var empty_finder = false;
            $('.finder').each(function (e) {
                empty_finder = ($(this).val().length == 0) ? true : empty_finder;
            });

            if (!empty_finder) {
                event.preventDefault();
                var $rules_to_save = $('#_data_to_save').val().split(','),
                    $rules_to_remove = $('#_data_to_remove').val().split(','),
                    $rules = $('._rule').serialize();
                var post_data = 'action=save_rules_action&_rules_to_save=' + $rules_to_save + '&_rules_to_remove=' + $rules_to_remove + '&' + $rules;

                $('#yith_wcgeoip_rules_panel').block({message: null, overlayCSS: {background: "#fff", opacity: .6}});

                $.post(ajaxurl, post_data).success(function (data) {
                    $('#_data_to_save').val('');
                    $('#_data_to_remove').val('');
                    for (var i = 0; i < data.length; i++) {
                        $('#_rules_' + data[i].index + '_rule_ID').val(data[i].id_rule);
                    }
                    $('#yith_wcgeoip_rules_panel').unblock();
                });
            }
        });

        $('#yith_wcgeoip_rules_panel').on('click', '.remove_rule', function (event) {
            event.preventDefault();
            var $_data_to_remove = $('#_data_to_remove'),
                $_rule_row = $(this).closest('.yith_wcgeoip_rule_row'),
                $_id = $_rule_row.attr('id'),
                $_id_rule = $('#_rules_' + $_id + '_rule_ID').val();

            if ('new' != $_id_rule) {
                if ($_data_to_remove.val().split(',').indexOf($_id_rule) < 0) {
                    if (0 == $_data_to_remove.val().length) {
                        $_data_to_remove.val($_id_rule);
                    } else {
                        $_data_to_remove.val($_data_to_remove.val() + ',' + $_id_rule);
                    }
                }
                $_data_to_remove.val($_data_to_remove.val().replace(',,', ','));
            }
            var regular = new RegExp($_id + ',?');
            $('#_data_to_save').val($('#_data_to_save').val().replace(regular, ''));

            $_rule_row.remove();
            refresh_position();
        });

        $('.yith_geoip_table_rules').sortable({
            stop: function (event, ui) {
                refresh_position();
            },
        });

    }

    function add_rule(event) {
        event.preventDefault();
        $('#yith_wcgeoip_rules_panel').block({message: null, overlayCSS: {background: "#fff", opacity: .6}});

        var post_data =
            {
                action: 'print_rule_row_action',
                index : $('.yith_wcgeoip_rule_row').length
            };

        $.post(ajaxurl, post_data).success(function (data) {
            $('#yith_wcgeoip_rules_panel').find('table').find('tbody').append(data);
            $('.wc-enhanced-select').trigger('wc-enhanced-select-init');
            $('#yith_wcgeoip_rules_panel').unblock();
        });
    }

    function finder_source(request, response) {
        var post_data =
            {
                dataType: 'json',
                action  : 'finder_source_action',
                data    : {
                    term  : request.term,
                    type  : request.type,
                    select: request.select
                }
            };

        $.post(ajaxurl, post_data).success(function (data) {
            var result = new Array();
            for (var i = 0; i < data.length; i++) {
                var permalink = check_permalink(data[i].permalink);
                var item = {
                    'id'       : data[i].id,
                    'title'    : data[i].title,
                    'permalink': permalink
                };
                result.push(item);
            }

            response(result);
        });
    }

    function refresh_position() {
        $('._rule_order').each(function (i) {
            $(this).val(i);
            $(this).trigger('change');
        });
    }

    function check_permalink(permalink) {

        if (permalink) {
            var question_pos = permalink.indexOf('?');

            if (0 <= question_pos) {
                var split_char = ('/' == permalink.substring(question_pos - 1, question_pos)) ? true : false;

                if (!split_char) {
                    var new_permalink = permalink.slice(0, question_pos) + '/' + permalink.slice(question_pos);
                    permalink = new_permalink;
                }
            }
        }
        return permalink;
    }

});