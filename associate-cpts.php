<?php
/*
Plugin Name: Gerenciamento de Empresas e Vagas
Description: Plugin para gerenciar associação entre empresas e vagas com controle de acesso para autores.
Author: Pablo
Version: 2.3
*/

// Registra o Custom Post Type "Empresas"
function criar_cpt_empresas() {
    register_post_type('empresas', array(
        'labels' => array(
            'name' => 'Empresas',
            'singular_name' => 'Empresa',
            'add_new_item' => 'Adicionar Nova Empresa',
            'edit_item' => 'Editar Empresa',
            'new_item' => 'Nova Empresa',
            'view_item' => 'Visualizar Empresa',
            'not_found' => 'Nenhuma empresa encontrada',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'capability_type' => 'post',
        'capabilities' => array(
            'create_posts' => 'do_not_allow', // bloqueia criação para todos exceto admins.
        ),
        'map_meta_cap' => true,
    ));
}
add_action('init', 'criar_cpt_empresas');

// Registra o CPT  "Vagas"
function criar_cpt_vagas() {
    register_post_type('vagas', array(
        'labels' => array(
            'name' => 'Vagas',
            'singular_name' => 'Vaga',
            'add_new_item' => 'Adicionar Nova Vaga',
            'edit_item' => 'Editar Vaga',
            'new_item' => 'Nova Vaga',
            'view_item' => 'Visualizar Vaga',
            'not_found' => 'Nenhuma vaga encontrada',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'custom-fields'),
    ));
}
add_action('init', 'criar_cpt_vagas');

// Adiciona campos personalizados ao CPT Vagas
function adicionar_campos_personalizados_vagas() {
    add_meta_box(
        'dados_vaga',
        'Detalhes da Vaga',
        'renderizar_campos_vaga',
        'vagas',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'adicionar_campos_personalizados_vagas');

function renderizar_campos_vaga($post) {
    $salario = get_post_meta($post->ID, '_salario', true);
    $telefone = get_post_meta($post->ID, '_telefone', true);
    ?>
    <label for="salario">Salário:</label>
    <input type="text" id="salario" name="salario" value="<?php echo esc_attr($salario); ?>" placeholder="Exemplo: R$ 2.000,00" />
    <input type="checkbox" id="a_combinar" name="a_combinar" <?php checked($salario, 'A combinar'); ?> />
    <label for="a_combinar">A combinar</label>
    <br><br>
    <label for="telefone">Telefone para contato:</label>
    <input type="text" id="telefone" name="telefone" value="<?php echo esc_attr($telefone); ?>" placeholder="Exemplo: (99) 99999-9999" />
    <?php
}

function salvar_campos_personalizados_vagas($post_id) {
    if (array_key_exists('salario', $_POST)) {
        $salario = $_POST['a_combinar'] ? 'A combinar' : sanitize_text_field($_POST['salario']);
        update_post_meta($post_id, '_salario', $salario);
    }

    if (array_key_exists('telefone', $_POST)) {
        update_post_meta($post_id, '_telefone', sanitize_text_field($_POST['telefone']));
    }
}
add_action('save_post', 'salvar_campos_personalizados_vagas');

// Filtra empresas e vagas para o autor
function filtrar_vagas_empresas_autor($query) {
    if (!is_admin() || !$query->is_main_query() || current_user_can('administrator')) {
        return;
    }

    $user_id = get_current_user_id();
    $empresa_associada = get_user_meta($user_id, '_empresa_associada', true);

    if ($query->query['post_type'] === 'empresas') {
        $query->set('post__in', array($empresa_associada));
    }

    if ($query->query['post_type'] === 'vagas') {
        $query->set('author', $user_id); // Mostra apenas as vagas do autor atual
    }
}
add_action('pre_get_posts', 'filtrar_vagas_empresas_autor');

// Oculta itens de menu para autores
function ocultar_itens_menu_autores() {
    if (!current_user_can('edit_others_posts')) {
        remove_menu_page('edit.php'); // Postagens
        remove_menu_page('edit-comments.php'); // Comentários
        remove_menu_page('themes.php'); // Aparência
        remove_menu_page('plugins.php'); // Plugins
        remove_menu_page('tools.php'); // Ferramentas
        remove_menu_page('options-general.php'); // Configurações
        remove_menu_page('edit.php?post_type=elementor_library'); // Modelos
        remove_submenu_page('edit.php?post_type=empresas', 'post-new.php?post_type=empresas'); // Remove botão "Adicionar Nova Empresa"
    }
}
add_action('admin_menu', 'ocultar_itens_menu_autores', 999);

// Remove o campo de associação de empresas para autores
function limitar_associacao_empresa($user) {
    if (!current_user_can('administrator')) {
        return;
    }

    $empresas = get_posts(array(
        'post_type' => 'empresas',
        'posts_per_page' => -1,
    ));
    $empresa_associada = get_user_meta($user->ID, '_empresa_associada', true);
    ?>
    <h3>Empresa Associada</h3>
    <table class="form-table">
        <tr>
            <th><label for="empresa_associada">Selecionar Empresa</label></th>
            <td>
                <select name="empresa_associada" id="empresa_associada">
                    <option value="">Nenhuma</option>
                    <?php foreach ($empresas as $empresa) : ?>
                        <option value="<?php echo esc_attr($empresa->ID); ?>" <?php selected($empresa_associada, $empresa->ID); ?>>
                            <?php echo esc_html($empresa->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Selecione a empresa associada a este usuário.</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'limitar_associacao_empresa');
add_action('edit_user_profile', 'limitar_associacao_empresa');

// Salva empresa associada ao usuário
function salvar_empresa_usuario($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    if (isset($_POST['empresa_associada'])) {
        update_user_meta($user_id, '_empresa_associada', sanitize_text_field($_POST['empresa_associada']));
    }
}
add_action('personal_options_update', 'salvar_empresa_usuario');
add_action('edit_user_profile_update', 'salvar_empresa_usuario');

