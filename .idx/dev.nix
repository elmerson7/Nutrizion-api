# To learn more about how to use Nix to configure your environment
# see: https://developers.google.com/idx/guides/customize-idx-env
{ pkgs, ... }: {
  # Which nixpkgs channel to use.
  channel = "stable-24.05"; # or "unstable"
  services.docker.enable = true;

  # Use https://search.nixos.org/packages to find packages
  packages = [
    pkgs.php82
    # pkgs.go
    # pkgs.python311
    # pkgs.nodejs_20
    # pkgs.nodePackages.nodemon
    pkgs.htop
    pkgs.nano
    pkgs.openssh
    pkgs.gnutar
    pkgs.docker-compose
  ];

  # Sets environment variables in the workspace
  env = {};
  idx = {
    # Search for the extensions you want on https://open-vsx.org/ and use "publisher.id"
    extensions = [
      # "vscodevim.vim"
      "ms-azuretools.vscode-docker"
      "eamodio.gitlens"
      "ms-vscode.test-adapter-converter"
      "Atishay-Jain.All-Autocomplete"
      "Durzn.brackethighlighter"
      "earshinov.simple-alignment"
      "naumovs.color-highlight"
      "oderwat.indent-rainbow"
      "PKief.material-icon-theme"
      "zobo.php-intellisense"
    ];

    # Enable previews
    previews = {
      enable = true;
      previews = {
        # web = {
        #   # Example: run "npm run dev" with PORT set to IDX's defined port for previews,
        #   # and show it in IDX's web preview panel
        #   command = ["npm" "run" "dev"];
        #   manager = "web";
        #   env = {
        #     # Environment variables to set for your server
        #     PORT = "$PORT";
        #   };
        # };
      };
    };

    # Workspace lifecycle hooks
    workspace = {
      # Runs when a workspace is first created
      onCreate = {
        # Example: install JS dependencies from NPM
        # npm-install = "npm install";
      };
      # Runs when the workspace is (re)started
      onStart = {
        # Example: start a background task to watch and re-build backend code
        # watch-backend = "npm run watch-backend";
        # Inicia el contenedor Docker
        start-docker-container = ''
          docker start php_app
        '';
      };
    };
  };
}
