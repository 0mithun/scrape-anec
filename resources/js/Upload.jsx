import React, { Component } from 'react';

class Upload extends Component {

    file = '';
    state = {
        progress : 0,
    }
   upload = ()=>{
   	axios.post(url, data, {
		onUploadProgress :(event) => {
			let complete = Math.ceil( (event.loaded / event.total) * 100);
			this.setState({progress: complete});
		}
   	});
   }
    render() {
        return (
            <div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style={{ width: this.progress +'%' }} aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{this.progress} %</div>
                </div>

                <form action="" onSubmit={this.upload}>

                    <div className="form-group">
                        <input type="text"  className="form-control" onChange={ (e) => this.file = e.target.value }  />
                    </div>

                    <button className="btn btn-primary" type="submit">Upload</button>
                </form>

            </div>
        );
    }
}

export default Upload;
