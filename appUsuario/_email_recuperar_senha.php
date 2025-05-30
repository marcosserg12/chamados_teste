Olá, <?php echo $data['ds_nome']; ?> <br>

<p>
    Recebemos um pedido para redefinir sua senha no sistema Ibranutro.
</p>

<p>
    <b>Usuário:</b> <?php echo $data['ds_usuario']; ?> <br>
    <b>E-mail:</b> <?php echo $data['ds_email']; ?> <br>

    <h3><a href="<?php echo absolute_url(); ?>/redefinir_senha.php?token=<?php echo $data['token']; ?>">Clique aqui para redefinir sua senha</a></h3>
</p>

<p>
    Atenção! Caso não tenha solicitado a alteração da senha, por favor, desconsidere esta mensagem e sua senha atual será mantida.
</p>

<p>
    Atenciosamente<br>Equipe Ibranutro
</p>