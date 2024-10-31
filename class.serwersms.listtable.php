<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class SerwerSms_List_Table extends WP_List_Table {

    var $table_name;
    var $data = array();
    var $columns = array();
    var $sortable = array();
    var $per_page;
    var $total_items;
    var $bulk_actions = true;

    function get_columns() {
        
        if($this->bulk_actions){
            $columns = array_merge(array(
                'cb' => '<input type="checkbox" />'
                    ), $this->columns);
        } else {
            $columns = $this->columns;
        }
        
        return $columns;
    }

    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->sortable;
        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->set_pagination_args(array(
            'total_items' => $this->total_items,
            'per_page' => $this->per_page,
        ));
        $this->items = $this->data;
    }

    function column_default($item, $column_name) {

        return $item[$column_name];
    }

    function usort_reorder($a, $b) {
        $orderby = (!empty($_GET['orderby']) ) ? sanitize_text_field($_GET['orderby']) : key($this->columns);
        $order = (!empty($_GET['order']) ) ? sanitize_text_field($_GET['order']) : 'asc';
        $result = strcmp($a[$orderby], $b[$orderby]);
        return ( $order === 'asc' ) ? $result : -$result;
    }

    function column_first($item) {
        $gr_id = (isset($item['group_id'])) ? (int) $item['group_id'] : null;
        $send_url = wp_nonce_url(sprintf('?page=%s&id=%s&source=%s', 'serwersms_message', $item['id'],$this->table_name),'send_item_'.$item['id']);
        $edit_url = wp_nonce_url(sprintf('?page=%s&action=%s&id=%s&group_id=%s', sanitize_text_field($_REQUEST['page']), 'edit', $item['id'],$gr_id),'edit_item_'.$item['id']);
        $delete_url = wp_nonce_url(sprintf('?page=%s&action=%s&id=%s', sanitize_text_field($_REQUEST['page']), 'delete', $item['id']),'delete_item_'.$item['id']);
        $actions = array(
            'send' => '<a href="'.$send_url.'">'.__('Send SMS','serwersms').'</a>',
            'edit' => '<a href="'.$edit_url.'">'.__('Edit','serwersms').'</a>',
            'delete' => '<a href="'.$delete_url.'">'.__('Delete','serwersms').'</a>',
        );

        return sprintf('%1$s %2$s', $item[key($this->columns)], $this->row_actions($actions));
    }

    function get_bulk_actions() {
        
        if($this->bulk_actions){
            $actions = array(
                'delete' => __('Delete','serwersms')
            );
        } else {
            $actions = array();
        }
        
        return $actions;
    }

    function column_cb($item) {
        
        if($this->bulk_actions){
            return sprintf(
                    '<input type="checkbox" name="id[]" value="%s" />', $item['id']
            );
        }
    }
    
    function no_items() {
        _e('Not found data', 'serwersms');
    }

}
