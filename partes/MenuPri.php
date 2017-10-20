		<!-- menu escamoteável -->
		<div class="header">
			<div class="container">
				<!-- menu de navegação -->
				<div class="navigation" id="menupri" >
					<div class="row">
						<!-- Navigation Menu Link Lists -->
						<!-- Menu dos autorizadores     -->
						<div class="col-md-2 col-sm-4 col-xs-6 aut">
							<div class="menu" >
								<span class="br-lblue" class="admmenu">
									<i class="glyphicon glyphicon-eye-open"></i> &nbsp; Autorizadores
								</span>
								<div class="menu-list">
									<ul>
										<li>
											<a href="autCadFunc.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Cadastro de funcionários" 
												 class="aut" style="color:gold ;" >
												<i class="glyphicon glyphicon-user" ></i>
												Cadastro
											</a>
										<li>
											<a href="autAlocaFunc.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Alocar pessoas" 
												 class="aut" style="color:darksalmon ;" >
												<i class="glyphicon glyphicon-time"></i>
												Alocar pessoas
											</a>
										</li>
										<li>
											<a href="autAdmApar.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Administrar aparelhos" 
												 class="aut" style="color: blueviolet;">
												<i class="glyphicon "></i>
												Administrar aparelhos
											</a>
										</li>
										<li>
											<a href="autTodosFunc.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Todos os funcionários" 
												 class="aut" style="color: DarkGreen ;" >
												<i class="typcn typcn-group"></i>
												Todos Funcionários 
											</a>
										</li>
										<!-- pendencias só via todosFuncionarios
										<li>
											<a href="autPenden.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Mostra pendencias de funcionários" 
												 class="aut" style="color: DarkGoldenRod;" >
												<i class="glyphicon glyphicon-time"></i> 
												Pendências
											</a>
										</li>
										-->
										<!-- autorização de ausências e correção a partir de todos Funcionarios
										<li>
											<a href="autAusencias.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Autorizar ausencias a funcionários"  
												 class="aut" style="color: green;" >
												<i class="glyphicon glyphicon-log-out"></i> 
												Autorizar ausências
											</a>
										</li>
										-->
										<li>
											<a href="autFechamento.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Fechamento mensal de UOR"  
												 class="aut" style="color: blue;">
												<i class="glyphicon glyphicon-calendar" ></i> 
												Fechamento Mensal
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="col-md-2 col-sm-5 col-xs-7 adm">
							<div class="menu">
								<span class="br-green"><i class="glyphicon glyphicon-adjust"></i> &nbsp; Administradores</span>
								<div class="menu-list">
									<ul>
										<li>
											<a href="admUsuarios.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Cadastramento de usuários" 
												 class="adm" >
												<i class="glyphicon glyphicon-user yellow"></i> 
												Usuários
											</a>
										</li>
										<li>
											<a href="admParametros.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Adequar parametros do sistema" 
												 class="adm" >
												<i class="glyphicon glyphicon-adjust red"></i> 
												Parametros
											</a>
										</li>
										<li>
											<a href="admLog.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Acesso ao Log do sistema" 
												 class="adm" >
												<i class="glyphicon glyphicon-list red"></i> 
												LOG
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="col-md-3 col-sm-5 col-xs-7">
							<div class="menu">
								<span class="br-pastel"><i class="glyphicon glyphicon-file"></i> &nbsp; Funcionários</span>
								<div class="menu-list">
									<ul>
										<li><a href="funAcesso.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Acesso do funcionário ao próprio ponto" 
												 class="fun" >
												<i class="glyphicon glyphicon-dashboard orange"></i> 
												Acesso ao Ponto
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>    <!-- menu de navegação -->
			</div>      <!-- container -->
		</div>        <!-- menu escamoteável -->
    
		<!-- botão de controle do menu   -->
    <div class="menu-btn" id="menu" >
      <a href="#"
         ttip="ttip" data-placement="bottom" data-original-title="Mostra/esconde o menu">
				Menu 
				<i class="glyphicon glyphicon-align-left"></i>
      </a>
    </div>
