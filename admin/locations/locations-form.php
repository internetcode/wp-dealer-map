<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<button class="dealer_map-latlng button"> <?php esc_html_e('Geocode', 'wp-dealer-map') ?></button>

        <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
            <tbody>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="name"><?php esc_html_e('Name', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['name'])) ?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Store name', 'wp-dealer-map')?>" required autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="address"><?php esc_html_e('Address', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="address" name="address" type="address" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['address'])) ?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Store Address', 'wp-dealer-map')?>" required autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="lat"><?php esc_html_e('Latitude', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="latitude" name="lat" type="number" step="0.000001" style="width: 95%" value="<?php echo esc_attr($item['lat'])?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Latitude', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="lng"><?php esc_html_e('Longitude', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="longitude" name="lng" type="number" step="0.000001" style="width: 95%" value="<?php echo esc_attr($item['lng'])?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Longitude', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="active"><?php esc_html_e('Active', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="active" name="active" type="number" min="0" max="1" step="1" style="width: 95%" value="<?php echo esc_attr($item['active'])?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Active 1 or 0', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="address2"><?php esc_html_e('Address 2', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="address2" name="address2" type="address" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['address2'])) ?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Store Address2', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="city"><?php esc_html_e('City', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="city" name="city" type="text" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['city'])) ?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('City', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="state"><?php esc_html_e('State', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="state" name="state" type="text" style="width: 95%" value="<?php echo esc_attr($item['state'])?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('State', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="zip"><?php esc_html_e('Postal / Zip', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="zip" name="zip" type="text" style="width: 95%" value="<?php echo esc_attr($item['zip'])?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Postal / Zip', 'wp-dealer-map')?>" autocomplete="off" maxlength="5">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="country"><?php esc_html_e('Country', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="country" name="country" type="text" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['country'])) ?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Country', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="description"><?php esc_html_e('Description', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="description" name="description" type="text" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['description'])) ?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Description', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="phone"><?php esc_html_e('Phone', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="phone" name="phone" type="text" style="width: 95%" value="<?php echo esc_attr($item['phone'])?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Phone', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="fax"><?php esc_html_e('Fax', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="fax" name="fax" type="text" style="width: 95%" value="<?php echo esc_attr($item['fax'])?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Fax', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="url"><?php esc_html_e('Website URL', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="url" name="url" type="url" style="width: 95%" value="<?php echo esc_attr($item['url'])?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Website URL', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="email"><?php esc_html_e('Email', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['email'])) ?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Email', 'wp-dealer-map')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label class="disabled" for="thumb_id"><?php esc_html_e('Image thumbnail', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="thumb_id" name="thumb_id" type="text" style="width: 95%" value="<?php echo esc_attr($item['thumb_id'])?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Image thumbnail', 'wp-dealer-map')?>" autocomplete="off" disabled="true">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="proseries"><?php esc_html_e('PRO', 'wp-dealer-map')?></label>
                </th>
                <td>
                    <input id="proseries" name="proseries" type="number" min="0" max="1" step="1" style="width: 95%" value="<?php echo esc_attr($item['proseries'])?>"
                            size="50" class="code" placeholder="<?php echo esc_attr('Pro Series 1 or 0', 'wp-dealer-map')?>" required autocomplete="off">
                </td>
            </tr>

            </tbody>
        </table>
        <button class="dealer_map-latlng button"> <?php esc_html_e('Geocode', 'wp-dealer-map') ?></button>
        <div id="dealer_map-map" class="dealer-map-admin"></div>
