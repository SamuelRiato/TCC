unit TelaLogin;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs,
  FMX.Controls.Presentation, FMX.StdCtrls, FMX.Objects, FMX.Layouts, FMX.Edit;

type
  Tfrmlogin = class(TForm)
    Layout1: TLayout;
    Layout2: TLayout;
    Image1: TImage;
    Layout3: TLayout;
    Label1: TLabel;
    Label2: TLabel;
    Layout4: TLayout;
    Label3: TLabel;
    edtemail: TEdit;
    Label4: TLabel;
    edtsenha: TEdit;
    Layout5: TLayout;
    Label5: TLabel;
    RoundRect1: TRoundRect;
    btnentrar: TSpeedButton;
    Layout6: TLayout;
    Label6: TLabel;
    Button1: TButton;
    procedure lblcadalunoClick(Sender: TObject);
    procedure SpeedButton1Click(Sender: TObject);
  private
    { Private declarations }
  public
    { Public declarations }
  end;

var
  frmlogin: Tfrmlogin;

implementation

{$R *.fmx}

uses TelaCadAluno, TelaInicial;

procedure Tfrmlogin.lblcadalunoClick(Sender: TObject);
begin
  FrmCadAluno.Show;
end;

procedure Tfrmlogin.SpeedButton1Click(Sender: TObject);
begin
 FrmCadAluno.show;
end;

end.
