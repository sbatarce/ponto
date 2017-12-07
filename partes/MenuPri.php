		<!--	div de wait -->											
		<div id="wait" class="waitajax"
				 style="display:none;width:69px;height:89px;
				 position:absolute;top:30%;left:50%;padding:2px;z-index: 2147483647;">
			<img src='imagens/espera.gif' width="128" height="128" /><br>
			Aguarde...
		</div>
		
		<!-- menu escamoteável -->
		<div class="header">
			<div class="container">
				<!-- menu de navegação -->
				<div class="navigation" id="menupri" >
					<div class="row">
						<!-- Menus por tipo de usuário   -->
						<!-- Menu dos super usuários     
						<div class="col-md-2 col-sm-5 col-xs-7 adm">
							<div class="menu adm">
								<span class="br-green" class="admmenu">
									<i class="glyphicon glyphicon-adjust"></i> 
									&nbsp; Administradores
								</span>
								<div class="menu-list adm">
									<ul>
										<li>
											<a href="autAtribuir.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Administração de Autorizadores" 
												 class="adm" style="color:red ;">
												<i class="glyphicon glyphicon-eye-open red"></i> 
												Autorizadores
											</a>
										</li>
										<li>
											<a href="admUsuarios.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Cadastramento de usuários" 
												 class="adm" style="color:goldenrod ;" >
												<i class="glyphicon glyphicon-user yellow"></i> 
												Usuários
											</a>
										</li>
										<li>
											<a href="admParametros.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Adequar parametros do sistema" 
												 class="adm" >
												<i class="glyphicon glyphicon-wrench red"></i> 
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
			-->
						<!-- Menu dos autorizadores     -->
						<div class="col-md-2 col-sm-4 col-xs-6 aut">
							<div class="menu aut" >
								<span class="br-lblue" class="admmenu">
									<i class="glyphicon glyphicon-eye-open"></i> 
									&nbsp; Autorizadores
								</span>
								<div class="menu-list">
									<ul>
										<li>
											<a href="autAtribuir.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Cadastro de funcionários" 
												 class="aut" style="color:red ;" >
												<i class="glyphicon glyphicon-eye-open red" ></i>
												Autorizadores
											</a>
										</li>
										<li>
											<a href="autCadFunc.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Cadastro de funcionários" 
												 class="aut" style="color:gold ;" >
												<i class="glyphicon glyphicon-user" ></i>
												Cadastro
											</a>
										</li>
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
												<i class="glyphicon glyphicon-scale"></i>
												Aparelhos
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
										<li>
											<a href="autFechamento.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Fechamento de um ou mais funcionários da UOR"  
												 class="aut" style="color: blue;">
												<i class="glyphicon glyphicon-calendar" ></i> 
												Fechamento
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<!-- Menu dos funcionários     -->
						<div class="col-md-3 col-sm-5 col-xs-7 fun">
							<div class="menu fun">
								<span class="br-pastel"><i class="glyphicon glyphicon-file"></i> 
									&nbsp; Funcionários
								</span>
								<div class="menu-list">
									<ul>
										<li><a href="funAcesso.php" ttip="ttip" data-placement="bottom" 
												 data-original-title="Acesso do funcionário ao próprio ponto" 
												 class="fun" >
												<i class="glyphicon glyphicon-dashboard orange"></i> 
												Acesso ao Ponto
											</a>
										</li>
										<li><a href="partes/Manual_Sistema_de_Ponto.pdf" ttip="ttip" 
													 data-placement="bottom" 
													 data-original-title="ajuda do ponto" 
													 class="fun" >
												<i class="glyphicon glyphicon-question-sign green"></i> 
												Ajuda
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
