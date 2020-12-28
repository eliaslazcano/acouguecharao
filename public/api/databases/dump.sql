create table produtos
(
	id int auto_increment
		primary key,
	nome varchar(128) null,
	codigo varchar(128) null,
	preco double null,
	criado_em datetime default current_timestamp() null,
	alterado_em datetime default current_timestamp() null,
	ativo tinyint(1) default 1 not null
)
comment 'Produtos para venda';

create table usuarios
(
	id int auto_increment
		primary key,
	username varchar(50) not null,
	senha varchar(32) null,
	nome varchar(64) null,
	apelido varchar(64) null,
	email varchar(128) null,
	cargo varchar(64) null,
	ativo tinyint(1) default 1 null,
	avatar mediumblob null,
	constraint usuarios_username_uindex
		unique (username)
);

create table sessoes
(
	id int auto_increment
		primary key,
	usuario int not null,
	segredo varchar(32) not null,
	datahora datetime default current_timestamp() null,
	ip varchar(15) null,
	constraint sessoes_usuarios_id_fk
		foreign key (usuario) references usuarios (id)
			on update cascade on delete cascade
);

create table vendas
(
	id int auto_increment
		primary key,
	datahora datetime default current_timestamp() not null,
	usuario int null comment 'Usuario que registrou a venda',
	desconto double default 0 null comment 'Valor de desconto dado ao fechar a conta',
	constraint vendas_usuarios_id_fk
		foreign key (usuario) references usuarios (id)
			on update cascade on delete set null
)
comment 'Vendas realizadas';

create table vendas_produtos
(
	id int auto_increment
		primary key,
	venda int not null,
	produto int null,
	quantidade double not null comment 'Quantidade por unidade ou por quilo',
	preco double null comment 'Valor cobrado pela unidade vendida',
	constraint vendas_produtos_produtos_id_fk
		foreign key (produto) references produtos (id)
			on update cascade on delete set null,
	constraint vendas_produtos_vendas_id_fk
		foreign key (venda) references vendas (id)
			on update cascade on delete cascade
)
comment 'Produtos contidos nas vendas';


