#!/usr/bin/perl

use File::Find;
use strict;
use warnings;

#variable globales

my $NOM_FIC_SAL = "atos_re_buscaTodo_Informe_";
my $RUT_COM = "/aplicaciones/atos/CRPR/COMPILACION/Comunes";
my $genML = "$RUT_COM/generacion_FML";

my $ruta = "/aplicaciones/atos/CRPR/COMPILACION/fuentes";
my $fecha = "";
my $ruta_salida = "/aplicaciones/atos/CRPR/COMPILACION/Mejoras/Informes";
my $separador_inicio = "INICIO SERVIDOR ***********************************************";
my $separador_final  = "FIN SERVIDOR **************************************************";
my $iden_servicio = "Servicio=";
my $iden_fglobal  = "Global_f=";
my $iden_f        = "_f=";
my $iden_servidor = "Servidor=";
my $iden_bulk     = "Bulk=";
my $iden_binario  = "Binario=";
my $iden_ruta  = "Ruta=";
my $iden_make  = "Make=";

#Leemos los parametros de entrada

if($#ARGV == 0)
{
  $fecha = $ARGV[0];
}
elsif($#ARGV == 1)
{
  $ruta = $ARGV[0];
  unless (-e $ruta)
  {
    print "No existe la ruta indicada por parametro\n";
    exit;
  }
  $fecha = $ARGV[1];
}
else
{
    print "Ejecutar: [buscaTodo.pl ruta(opcional) ddmmaaaa ]\n";
    exit;
}

# Inicio logica

my $fic_salida = "$NOM_FIC_SAL$fecha.txt";

print "Buscando en " . $ruta . "\n";

open(OUT_FIC, ">$ruta_salida/$fic_salida");

find(\&busca_makes, $ruta);

close(OUT_FIC);

print "Fin busqueda, se ha creado el fichero [$fic_salida] en [$ruta_salida]\n";

sub busca_makes{

  my $elemento = $_;

  if(-f $elemento && $elemento =~ /\.mak$/)
  {
    $ruta = "$File::Find::dir";
    my $make = "$_";
    my $servidor = "";
    my $binario = "";
    my $servicio = "";
    my $_f = "";
    my $_fGlobal = "";
    print OUT_FIC "$separador_inicio\n";
    print OUT_FIC "$iden_make$make\n$iden_ruta$ruta\n";

    open (DATOS,"$File::Find::name");
    while (<DATOS>)
    {
        chomp;
        my $linea = $_;
        if($linea =~ m/SERVIDOR=([a-zA-Z0-9_]+)/) #servidor
        {
          $servidor=$1;
          print OUT_FIC "$iden_servidor$servidor\n";
          print OUT_FIC "$iden_bulk$servidor.bulk\n";

          open my $fh, $genML or die "Could not open $genML $!";
          my @buf = <$fh>;
          close($fh);

          my @lines = grep {/$servidor/} @buf;
          chomp(@lines);
          for my $str (@lines) {

            if($str =~ m/(\s*>>\s*\$[a-zA-Z_]+\/)([a-zA-Z0-9_]+_f$)/) # busca el _f global
            {
              $_fGlobal = $2;
            }
          }

          print OUT_FIC "$iden_f$servidor". "_f\n";
          print OUT_FIC "$iden_fglobal$_fGlobal\n";
        }
        if($linea =~ m/(\s*-v\s*-o\s*\$\([a-zA-Z_]+\)\/\$\()([a-zA-Z0-9_]+)/) #binario con $(nombre)
        {
          
          my $patron = $2 . "=([a-zA-Z0-9_]+)";

          open my $fh, $File::Find::name or die "Could not open $File::Find::name: $!";
          my @buf = <$fh>;
          close($fh);

          my @lines = grep {/$patron/} @buf;
          chomp(@lines);

          for my $str (@lines) {
            $patron = $2 . "=([a-zA-Z0-9_]+)";

            if($str =~ m/$patron/)
            {
              $binario = $1;
            }
          }

          print OUT_FIC "$iden_binario$binario\n";
        }
        if($linea =~ m/(\s*-v\s*-o\s*\$\([a-zA-Z_]+\)\/)([a-zA-Z_0-9]+)/) #binario con literal
        {
          $binario = $2;
          print OUT_FIC "$iden_binario$binario\n";
        }
        if($linea =~ m/(\s*mkfldhdr32\s*\$\([a-zA-Z_]+\)\/)([a-zA-Z0-9_]+_f$)/) # busca _f
        {
          $_f = $2;
          print OUT_FIC "$iden_f$_f\n"; #falta ir a buscar el _f al generacion_FML

          open my $fh, $genML or die "Could not open $genML $!";
          my @buf = <$fh>;
          close($fh);

          my @lines = grep {/$_f/} @buf;
          chomp(@lines);
          for my $str (@lines) {

            if($str =~ m/(\s*>>\s*\$[a-zA-Z_]+\/)([a-zA-Z0-9_]+_f$)/) # busca el _f global
            {
              $_fGlobal = $2;
            }
          }
          print OUT_FIC "$iden_fglobal$_fGlobal\n";

        }
        if($linea =~ m/(\s*-s\s*)([a-zA-Z_0-9]+)/) #servicio
        {
          $servicio= $2;
          print OUT_FIC "$iden_servicio$servicio\n";
        }
    }
    print OUT_FIC "$separador_final\n";
    close(DATOS);
  }
}
