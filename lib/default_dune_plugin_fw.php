<?php


require_once 'lib/action_factory.php';
require_once 'lib/dune_exception.php';
require_once 'lib/utils.php';


class DefaultDunePluginFw extends DunePluginFw
{
    public static $plugin_class_name = null;



    public function create_plugin()
    {
        return new DefaultDunePluginFw::$plugin_class_name;
    }



    public function call_plugin($call_ctx_json)
    {
        // $call_ctx = json_decode($call_ctx_json);
        // $ret = $this->call_plugin_impl($call_ctx);
        // return json_encode($ret);
        // HD::print_backtrace();
        // var_dump("call_ctx_json");
        hd_print(__METHOD__ . ":" . print_r($call_ctx_json, true));
        $a = json_encode(
                $this->call_plugin_impl(
                    json_decode($call_ctx_json)));
        //hd_print(__METHOD__ . ':' . print_r(json_decode($a), true));
        hd_print(__METHOD__ . ":" . print_r($a, true));
        // HD::print_backtrace();
        return $a;

    }



    protected function call_plugin_impl($call_ctx)
    {
        static $plugin;

        if (is_null($plugin))
        {
            try
            {
                //hd_print('Instantiating plugin...');
                $plugin = $this->create_plugin();
                //hd_print('Plugin instance created.');
            }
            catch (Exception $e)
            {
                hd_print('Error: can not instantiate plugin (' . $e->getMessage() . ')');

                return
                    array
                    (
                        PluginOutputData::has_data => false,
                        PluginOutputData::plugin_cookies => $call_ctx->plugin_cookies,
                        PluginOutputData::is_error => true,
                        PluginOutputData::error_action =>
                            ActionFactory::show_error(
                                true,
                                'System error',
                                array(
                                    'Can not create PHP plugin instance.',
                                    'Call the PHP plugin vendor.'))
                    );
            }
        }

        // assert($plugin);



        $out_data = null;

        try
        {
            $out_data = $this->invoke_operation($plugin, $call_ctx);
            // hd_print("out_data = ". print_r($out_data, true));
        }
        catch (DuneException $e)
        {
            hd_print("Error: DuneException caught: " . $e->getMessage());
            return
                array
                (
                    PluginOutputData::has_data => false,
                    PluginOutputData::plugin_cookies => $call_ctx->plugin_cookies,
                    PluginOutputData::is_error => true,
                    PluginOutputData::error_action => $e->get_error_action()
                );
        }
        catch (Exception $e)
        {
            hd_print("Error: Exception caught: " . $e->getMessage());

            return
                array
                (
                    PluginOutputData::has_data => false,
                    PluginOutputData::plugin_cookies => $call_ctx->plugin_cookies,
                    PluginOutputData::is_error => true,
                    PluginOutputData::error_action =>
                        ActionFactory::show_error(
                            true,
                            'System error',
                            array(
                                'Unhandled PHP plugin error.',
                                'Call the PHP plugin vendor.'))
                );
        }

        // Note: change_tv_favorites() may return NULL even if it's completed
        // successfully.

        $plugin_output_data = array
        (
            PluginOutputData::has_data => !is_null($out_data),
            PluginOutputData::plugin_cookies => $call_ctx->plugin_cookies,
            PluginOutputData::is_error => false,
            PluginOutputData::error_action => null
        );

        if ($plugin_output_data[PluginOutputData::has_data])
        {
            $plugin_output_data[PluginOutputData::data_type] =
                $this->get_out_type_code($call_ctx->op_type_code);

            $plugin_output_data[PluginOutputData::data] = $out_data;
        }

        return $plugin_output_data;
    }
}



DunePluginFw::$instance = new DefaultDunePluginFw();


?>
