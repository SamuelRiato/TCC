program Project1;

uses
  System.StartUpCopy,
  FMX.Forms,
  TelaInicial in 'TelaInicial.pas' {FrmInicial},
  TelaCadAluno in 'TelaCadAluno.pas' {FrmCadAluno};

{$R *.res}

begin
  Application.Initialize;
  Application.CreateForm(TFrmInicial, FrmInicial);
  Application.CreateForm(TFrmCadAluno, FrmCadAluno);
  Application.Run;
end.
