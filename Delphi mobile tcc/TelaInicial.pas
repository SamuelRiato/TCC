unit TelaInicial;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs,
  FMX.Controls.Presentation, FMX.StdCtrls, FMX.Layouts, FMX.Objects;

type
  TFrmInicial = class(TForm)
    Layout1: TLayout;
    Layout2: TLayout;
    Label1: TLabel;
    Image1: TImage;
    Label2: TLabel;
    Layout3: TLayout;
    Rectangle1: TRectangle;
    Rectangle2: TRectangle;
    Label4: TLabel;
    Rectangle3: TRectangle;
    Label6: TLabel;
    Label7: TLabel;
    Label8: TLabel;
    Layout4: TLayout;
    Layout5: TLayout;
    Layout6: TLayout;
    Label9: TLabel;
    Label10: TLabel;
    BtnProxTreino: TButton;
    Button1: TButton;
    Label3: TLabel;
    Label5: TLabel;
    Label11: TLabel;
    Label12: TLabel;
    Label13: TLabel;
    Label14: TLabel;
    Label15: TLabel;
    Label16: TLabel;
    Label17: TLabel;
    Label18: TLabel;
    Label19: TLabel;
    Label20: TLabel;
    procedure Label1Click(Sender: TObject);
  private
    { Private declarations }
  public
    { Public declarations }
  end;

var
  FrmInicial: TFrmInicial;

implementation

{$R *.fmx}

uses TelaCadAluno, TelaLogin;

procedure TFrmInicial.Label1Click(Sender: TObject);
begin
 label1.text:=('Olá'+ frmcadaluno.edtnomealuno.text);
end;

end.
