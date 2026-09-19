unit TelaCadAluno;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs,
  FMX.Controls.Presentation, FMX.Edit, FMX.Layouts, FMX.Objects;

type
  TFrmCadAluno = class(TForm)
    Layout1: TLayout;
    EdtNomeAluno: TEdit;
    Layout2: TLayout;
    Image1: TImage;
  private
    { Private declarations }
  public
    { Public declarations }
  end;

var
  FrmCadAluno: TFrmCadAluno;

implementation

{$R *.fmx}

end.
